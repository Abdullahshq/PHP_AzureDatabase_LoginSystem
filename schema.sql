CREATE TABLE Login (
    UniqueID INT IDENTITY(1,1) PRIMARY KEY,
    LoginUsername VARCHAR(50) NOT NULL UNIQUE,
    LoginPassword VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Permission VARCHAR(20) DEFAULT 'user',
    IPAddress VARCHAR(45),
    LastLoginTime DATETIME,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    ResetToken VARCHAR(255),
    ResetTokenExpiry DATETIME
);