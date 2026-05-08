-- Admin Dummy Account untuk Arradea Marketplace

-- Hapus admin lama jika sudah ada (opsional)
-- DELETE FROM users WHERE phone = '08123456789';

-- Insert admin account baru
INSERT INTO users (
    name, 
    phone, 
    password, 
    role, 
    is_seller, 
    phone_verified_at, 
    wilayah,
    created_at, 
    updated_at
) VALUES (
    'Admin Arradea',
    '08123456789',
    '$2y$12$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/M0a', -- password: admin123
    'admin',
    0,
    NOW(),
    'Jakarta',
    NOW(),
    NOW()
)
ON CONFLICT DO NOTHING;

-- Verify: Select admin account
SELECT id, name, phone, role, is_seller, created_at FROM users WHERE phone = '08123456789';
