USE tattoo;

-- Add home service columns to appointments table
ALTER TABLE appointments
ADD COLUMN is_home_service BOOLEAN DEFAULT FALSE,
ADD COLUMN address TEXT;

-- Add artist_id column to appointments table
ALTER TABLE appointments
ADD COLUMN artist_id INT,
ADD FOREIGN KEY (artist_id) REFERENCES users(id);

-- Add price column to appointments table
ALTER TABLE appointments
ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00;
