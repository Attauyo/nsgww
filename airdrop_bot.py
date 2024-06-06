import logging
import sqlite3
from telegram import Update, Bot
from telegram.ext import Updater, CommandHandler, CallbackContext
from datetime import datetime, timedelta

# Configure logging
logging.basicConfig(format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO)

# Telegram Bot Token
TELEGRAM_TOKEN = '7393453340:AAEGmI7DlaQmZ7M156flDrXiIK9j2U_A4rA'

# Placeholders for channel, group, YouTube, and Facebook URLs
CHANNEL_URL = 'https://t.me/YOUR_TELEGRAM_CHANNEL'
GROUP_URL = 'https://t.me/YOUR_TELEGRAM_GROUP'
YOUTUBE_URL = 'https://youtube.com/channel/YOUR_CHANNEL_ID'
FACEBOOK_URL = 'https://facebook.com/YOUR_PAGE_ID'

# Connect to SQLite database (or create it if it doesn't exist)
conn = sqlite3.connect('airdrop_bot.db')
cursor = conn.cursor()

# SQL to create the users table
create_table_sql = '''
CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY,
    tasks_completed BOOLEAN NOT NULL DEFAULT FALSE,
    referred_by INTEGER,
    balance INTEGER NOT NULL DEFAULT 0,
    last_click_time TEXT,
    FOREIGN KEY(referred_by) REFERENCES users(user_id)
);
'''

# SQL to create index on referred_by
create_index_sql = '''
CREATE INDEX IF NOT EXISTS idx_referred_by ON users(referred_by);
'''

# Execute the SQL
cursor.execute(create_table_sql)
cursor.execute(create_index_sql)
conn.commit()

# Function to start the bot
def start(update: Update, context: CallbackContext) -> None:
    user = update.message.from_user

    # Add user to database if not already present
    cursor.execute('SELECT * FROM users WHERE user_id = ?', (user.id,))
    if cursor.fetchone() is None:
        cursor.execute('''
        INSERT INTO users (user_id, tasks_completed, referred_by, balance, last_click_time) 
        VALUES (?, ?, ?, ?, ?)
        ''', (user.id, False, None, 0, None))
        conn.commit()

    update.message.reply_text(
        f"Welcome {user.first_name}! To participate in the airdrop, please complete the following tasks:\n"
        f"1. Join our Telegram Channel: {CHANNEL_URL}\n"
        f"2. Join our Telegram Group: {GROUP_URL}\n"
        f"3. Subscribe to our YouTube Channel: {YOUTUBE_URL}\n"
        f"4. Like our Facebook Page: {FACEBOOK_URL}\n"
        "After completing the tasks, use the /check command to verify."
    )

# Function to check task completion
def check(update: Update, context: CallbackContext) -> None:
    user = update.message.from_user

    # Placeholder for actual task verification logic
    tasks_completed = True

    if tasks_completed:
        cursor.execute('UPDATE users SET tasks_completed = ? WHERE user_id = ?', (True, user.id))
        conn.commit()
        cursor.execute('UPDATE users SET balance = balance + 100 WHERE user_id = ?', (user.id,))
        conn.commit()
        update.message.reply_text("Thank you for completing the tasks! You have been credited with the airdrop.")
    else:
        update.message.reply_text("You have not completed all tasks. Please try again.")

# Function to claim hourly rewards
def click(update: Update, context: CallbackContext) -> None:
    user = update.message.from_user
    now = datetime.now()

    cursor.execute('SELECT last_click_time, balance FROM users WHERE user_id = ?', (user.id,))
    result = cursor.fetchone()
    last_click_time = datetime.fromisoformat(result[0]) if result[0] else None
    balance = result[1]

    if last_click_time is None or (now - last_click_time) >= timedelta(hours=1):
        new_balance = balance + 10
        cursor.execute('''
        UPDATE users 
        SET balance = ?, last_click_time = ? 
        WHERE user_id = ?
        ''', (new_balance, now.isoformat(), user.id))
        conn.commit()
        update.message.reply_text("You have claimed your hourly reward of 10 tokens!")
    else:
        next_claim_time = last_click_time + timedelta(hours=1)
        wait_time = next_claim_time - now
        minutes, seconds = divmod(wait_time.seconds, 60)
        update.message.reply_text(f"You can claim your next reward in {minutes} minutes and {seconds} seconds.")

# Function to check user balance
def balance(update: Update, context: CallbackContext) -> None:
    user = update.message.from_user
    cursor.execute('SELECT balance FROM users WHERE user_id = ?', (user.id,))
    balance = cursor.fetchone()[0]
    update.message.reply_text(f"Your balance is: {balance} tokens.")

# Function to handle referrals
def referral(update: Update, context: CallbackContext) -> None:
    user = update.message.from_user
    referrer = context.args[0] if context.args else None
    if referrer:
        referrer_id = int(referrer)
        cursor.execute('SELECT * FROM users WHERE user_id = ?', (referrer_id,))
        if cursor.fetchone():
            cursor.execute('UPDATE users SET balance = balance + 50 WHERE user_id = ?', (referrer_id,))
            cursor.execute('UPDATE users SET referred_by = ? WHERE user_id = ?', (referrer_id, user.id))
            conn.commit()
            update.message.reply_text("Referral successful! Both you and your referrer have been rewarded.")
        else:
            update.message.reply_text("Invalid referral code.")
    else:
        update.message.reply_text("Please provide a referral code.")

# Function to handle withdrawals
def withdraw(update: Update, context: CallbackContext) -> None:
    user = update.message.from_user
    cursor.execute('SELECT balance FROM users WHERE user_id = ?', (user.id,))
    balance = cursor.fetchone()[0]
    if balance >= 100:  # Example minimum withdrawal balance
        new_balance = balance - 100
        cursor.execute('UPDATE users SET balance = ? WHERE user_id = ?', (new_balance, user.id))
        conn.commit()
        update.message.reply_text("Withdrawal successful! 100 tokens have been deducted from your balance.")
    else:
        update.message.reply_text("Insufficient balance for withdrawal.")

# Main function to run the bot
def main():
    updater = Updater(TELEGRAM_TOKEN)
    dispatcher = updater.dispatcher

    dispatcher.add_handler(CommandHandler("start", start))
    dispatcher.add_handler(CommandHandler("check", check))
    dispatcher.add_handler(CommandHandler("click", click))
    dispatcher.add_handler(CommandHandler("balance", balance))
    dispatcher.add_handler(CommandHandler("referral", referral, pass_args=True))
    dispatcher.add_handler(CommandHandler("withdraw", withdraw))

    updater.start_polling()
    updater.idle()

if __name__ == '__main__':
    main()
