
-- Create the users table
CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY,
    tasks_completed BOOLEAN NOT NULL DEFAULT FALSE,
    referred_by INTEGER,
    balance INTEGER NOT NULL DEFAULT 0,
    last_click_time TEXT,
    FOREIGN KEY(referred_by) REFERENCES users(user_id)
);

-- Create an index on the referred_by column for faster lookups
CREATE INDEX IF NOT EXISTS idx_referred_by ON users(referred_by);
