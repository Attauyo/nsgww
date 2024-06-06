import sqlite3

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
conn.close()

print("Database setup completed.")
