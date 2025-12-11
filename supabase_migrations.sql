-- ============================================
-- MIGRATIONS SUPABASE - BOUTIQUE DROPSHIPPING
-- ============================================

-- Extension pour UUID
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================
-- TABLE: customers
-- ============================================
CREATE TABLE IF NOT EXISTS customers (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email TEXT UNIQUE NOT NULL,
    name TEXT,
    phone TEXT,
    addresses JSONB DEFAULT '[]'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================
-- TABLE: suppliers
-- ============================================
CREATE TABLE IF NOT EXISTS suppliers (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name TEXT NOT NULL,
    api_type TEXT NOT NULL, -- 'aliexpress', 'cj_dropshipping', etc.
    api_key TEXT, -- Encrypted
    api_secret TEXT, -- Encrypted
    wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
    status TEXT DEFAULT 'active', -- 'active', 'inactive'
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- ============================================
-- TABLE: products
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name TEXT NOT NULL,
    description TEXT,
    supplier_price DECIMAL(10, 2) NOT NULL, -- Prix fournisseur (ex: 5$)
    selling_price DECIMAL(10, 2) NOT NULL, -- Prix vente (ex: 25$)
    margin DECIMAL(10, 2) GENERATED ALWAYS AS (selling_price - supplier_price) STORED,
    images TEXT[] DEFAULT '{}', -- Array d'URLs images
    supplier_id UUID REFERENCES suppliers(id) ON DELETE SET NULL,
    supplier_product_id TEXT, -- ID produit chez le fournisseur
    status TEXT DEFAULT 'active', -- 'active', 'inactive', 'out_of_stock'
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index pour recherche produits
CREATE INDEX IF NOT EXISTS idx_products_status ON products(status);
CREATE INDEX IF NOT EXISTS idx_products_supplier ON products(supplier_id);

-- ============================================
-- TABLE: orders
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    customer_id UUID REFERENCES customers(id) ON DELETE SET NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    supplier_cost DECIMAL(10, 2) DEFAULT 0.00, -- Coût total fournisseur
    margin DECIMAL(10, 2) DEFAULT 0.00, -- Marge totale réalisée
    status TEXT DEFAULT 'pending', -- 'pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'
    payment_status TEXT DEFAULT 'pending', -- 'pending', 'paid', 'failed', 'refunded'
    payment_id TEXT, -- ID transaction paiement
    payment_gateway TEXT, -- 'korapay', 'dpo', etc.
    shipping_address JSONB NOT NULL,
    tracking_number TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index pour recherche commandes
CREATE INDEX IF NOT EXISTS idx_orders_customer ON orders(customer_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_orders_payment_status ON orders(payment_status);

-- ============================================
-- TABLE: order_items
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    order_id UUID REFERENCES orders(id) ON DELETE CASCADE,
    product_id UUID REFERENCES products(id) ON DELETE SET NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL, -- Prix unitaire au moment de la commande
    supplier_price DECIMAL(10, 2) NOT NULL, -- Prix fournisseur au moment de la commande
    margin DECIMAL(10, 2) GENERATED ALWAYS AS ((unit_price - supplier_price) * quantity) STORED,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index
CREATE INDEX IF NOT EXISTS idx_order_items_order ON order_items(order_id);
CREATE INDEX IF NOT EXISTS idx_order_items_product ON order_items(product_id);

-- ============================================
-- TABLE: payments
-- ============================================
CREATE TABLE IF NOT EXISTS payments (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    order_id UUID REFERENCES orders(id) ON DELETE SET NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method TEXT DEFAULT 'card', -- 'card', 'mobile_money', etc.
    payment_gateway TEXT NOT NULL, -- 'korapay', 'dpo', etc.
    transaction_id TEXT UNIQUE,
    status TEXT DEFAULT 'pending', -- 'pending', 'success', 'failed', 'refunded'
    gateway_response JSONB, -- Réponse complète de la passerelle
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index
CREATE INDEX IF NOT EXISTS idx_payments_order ON payments(order_id);
CREATE INDEX IF NOT EXISTS idx_payments_transaction ON payments(transaction_id);
CREATE INDEX IF NOT EXISTS idx_payments_status ON payments(status);

-- ============================================
-- TABLE: supplier_orders
-- ============================================
CREATE TABLE IF NOT EXISTS supplier_orders (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    order_id UUID REFERENCES orders(id) ON DELETE CASCADE,
    supplier_id UUID REFERENCES suppliers(id) ON DELETE SET NULL,
    supplier_order_id TEXT, -- ID commande chez le fournisseur
    amount_paid DECIMAL(10, 2) NOT NULL,
    status TEXT DEFAULT 'pending', -- 'pending', 'paid', 'shipped', 'delivered', 'cancelled'
    tracking_number TEXT,
    supplier_response JSONB, -- Réponse API fournisseur
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index
CREATE INDEX IF NOT EXISTS idx_supplier_orders_order ON supplier_orders(order_id);
CREATE INDEX IF NOT EXISTS idx_supplier_orders_supplier ON supplier_orders(supplier_id);
CREATE INDEX IF NOT EXISTS idx_supplier_orders_status ON supplier_orders(status);

-- ============================================
-- TABLE: wallet_transactions
-- ============================================
CREATE TABLE IF NOT EXISTS wallet_transactions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    supplier_id UUID REFERENCES suppliers(id) ON DELETE CASCADE,
    order_id UUID REFERENCES orders(id) ON DELETE SET NULL,
    type TEXT NOT NULL, -- 'deposit', 'withdrawal', 'refund'
    amount DECIMAL(10, 2) NOT NULL,
    balance_before DECIMAL(10, 2) NOT NULL,
    balance_after DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index
CREATE INDEX IF NOT EXISTS idx_wallet_transactions_supplier ON wallet_transactions(supplier_id);
CREATE INDEX IF NOT EXISTS idx_wallet_transactions_order ON wallet_transactions(order_id);

-- ============================================
-- FUNCTIONS & TRIGGERS
-- ============================================

-- Function pour mettre à jour updated_at automatiquement
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Triggers pour updated_at
CREATE TRIGGER update_customers_updated_at BEFORE UPDATE ON customers
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_suppliers_updated_at BEFORE UPDATE ON suppliers
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_products_updated_at BEFORE UPDATE ON products
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_orders_updated_at BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_payments_updated_at BEFORE UPDATE ON payments
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_supplier_orders_updated_at BEFORE UPDATE ON supplier_orders
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ============================================
-- FUNCTION: Calculer marge commande
-- ============================================
CREATE OR REPLACE FUNCTION calculate_order_margin(order_uuid UUID)
RETURNS DECIMAL AS $$
DECLARE
    total_margin DECIMAL;
BEGIN
    SELECT COALESCE(SUM(margin), 0) INTO total_margin
    FROM order_items
    WHERE order_id = order_uuid;
    
    RETURN total_margin;
END;
$$ LANGUAGE plpgsql;

-- ============================================
-- FUNCTION: Débiter wallet fournisseur
-- ============================================
CREATE OR REPLACE FUNCTION debit_supplier_wallet(
    p_supplier_id UUID,
    p_amount DECIMAL,
    p_order_id UUID,
    p_description TEXT DEFAULT NULL
)
RETURNS BOOLEAN AS $$
DECLARE
    current_balance DECIMAL;
    new_balance DECIMAL;
BEGIN
    -- Récupérer balance actuelle
    SELECT wallet_balance INTO current_balance
    FROM suppliers
    WHERE id = p_supplier_id;
    
    -- Vérifier si balance suffisante
    IF current_balance < p_amount THEN
        RAISE EXCEPTION 'Balance insuffisante. Balance actuelle: %, Montant requis: %', current_balance, p_amount;
    END IF;
    
    -- Calculer nouvelle balance
    new_balance := current_balance - p_amount;
    
    -- Mettre à jour balance
    UPDATE suppliers
    SET wallet_balance = new_balance
    WHERE id = p_supplier_id;
    
    -- Enregistrer transaction
    INSERT INTO wallet_transactions (
        supplier_id,
        order_id,
        type,
        amount,
        balance_before,
        balance_after,
        description
    ) VALUES (
        p_supplier_id,
        p_order_id,
        'withdrawal',
        p_amount,
        current_balance,
        new_balance,
        p_description
    );
    
    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

-- ============================================
-- FUNCTION: Créditer wallet fournisseur
-- ============================================
CREATE OR REPLACE FUNCTION credit_supplier_wallet(
    p_supplier_id UUID,
    p_amount DECIMAL,
    p_description TEXT DEFAULT NULL
)
RETURNS BOOLEAN AS $$
DECLARE
    current_balance DECIMAL;
    new_balance DECIMAL;
BEGIN
    -- Récupérer balance actuelle
    SELECT wallet_balance INTO current_balance
    FROM suppliers
    WHERE id = p_supplier_id;
    
    -- Calculer nouvelle balance
    new_balance := current_balance + p_amount;
    
    -- Mettre à jour balance
    UPDATE suppliers
    SET wallet_balance = new_balance
    WHERE id = p_supplier_id;
    
    -- Enregistrer transaction
    INSERT INTO wallet_transactions (
        supplier_id,
        type,
        amount,
        balance_before,
        balance_after,
        description
    ) VALUES (
        p_supplier_id,
        'deposit',
        p_amount,
        current_balance,
        new_balance,
        p_description
    );
    
    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

-- ============================================
-- ROW LEVEL SECURITY (RLS)
-- ============================================

-- Activer RLS sur toutes les tables
ALTER TABLE customers ENABLE ROW LEVEL SECURITY;
ALTER TABLE suppliers ENABLE ROW LEVEL SECURITY;
ALTER TABLE products ENABLE ROW LEVEL SECURITY;
ALTER TABLE orders ENABLE ROW LEVEL SECURITY;
ALTER TABLE order_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments ENABLE ROW LEVEL SECURITY;
ALTER TABLE supplier_orders ENABLE ROW LEVEL SECURITY;
ALTER TABLE wallet_transactions ENABLE ROW LEVEL SECURITY;

-- ============================================
-- POLICIES: customers
-- ============================================
-- Les utilisateurs peuvent voir leur propre profil
CREATE POLICY "Users can view own profile"
ON customers FOR SELECT
USING (auth.uid()::text = id::text);

-- Les utilisateurs peuvent mettre à jour leur profil
CREATE POLICY "Users can update own profile"
ON customers FOR UPDATE
USING (auth.uid()::text = id::text);

-- ============================================
-- POLICIES: products
-- ============================================
-- Public peut voir les produits actifs
CREATE POLICY "Public can view active products"
ON products FOR SELECT
USING (status = 'active');

-- Admin peut tout faire sur les produits
CREATE POLICY "Admin full access products"
ON products FOR ALL
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- POLICIES: orders
-- ============================================
-- Les utilisateurs peuvent voir leurs propres commandes
CREATE POLICY "Users can view own orders"
ON orders FOR SELECT
USING (
    customer_id IN (
        SELECT id FROM customers WHERE id::text = auth.uid()::text
    )
);

-- Les utilisateurs peuvent créer leurs propres commandes
CREATE POLICY "Users can create own orders"
ON orders FOR INSERT
WITH CHECK (
    customer_id IN (
        SELECT id FROM customers WHERE id::text = auth.uid()::text
    )
);

-- Admin peut voir toutes les commandes
CREATE POLICY "Admin can view all orders"
ON orders FOR SELECT
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- Admin peut mettre à jour toutes les commandes
CREATE POLICY "Admin can update all orders"
ON orders FOR UPDATE
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- POLICIES: order_items
-- ============================================
-- Les utilisateurs peuvent voir les items de leurs commandes
CREATE POLICY "Users can view own order items"
ON order_items FOR SELECT
USING (
    order_id IN (
        SELECT id FROM orders
        WHERE customer_id IN (
            SELECT id FROM customers WHERE id::text = auth.uid()::text
        )
    )
);

-- Admin peut tout voir
CREATE POLICY "Admin can view all order items"
ON order_items FOR SELECT
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- POLICIES: suppliers
-- ============================================
-- Seuls les admins peuvent accéder aux fournisseurs
CREATE POLICY "Admin only suppliers"
ON suppliers FOR ALL
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- POLICIES: payments
-- ============================================
-- Les utilisateurs peuvent voir leurs propres paiements
CREATE POLICY "Users can view own payments"
ON payments FOR SELECT
USING (
    order_id IN (
        SELECT id FROM orders
        WHERE customer_id IN (
            SELECT id FROM customers WHERE id::text = auth.uid()::text
        )
    )
);

-- Admin peut tout voir
CREATE POLICY "Admin can view all payments"
ON payments FOR SELECT
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- POLICIES: supplier_orders
-- ============================================
-- Admin seulement
CREATE POLICY "Admin only supplier orders"
ON supplier_orders FOR ALL
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- POLICIES: wallet_transactions
-- ============================================
-- Admin seulement
CREATE POLICY "Admin only wallet transactions"
ON wallet_transactions FOR ALL
USING (
    EXISTS (
        SELECT 1 FROM auth.users
        WHERE auth.users.id = auth.uid()
        AND auth.users.raw_user_meta_data->>'role' = 'admin'
    )
);

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue pour statistiques commandes
CREATE OR REPLACE VIEW order_stats AS
SELECT 
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    SUM(margin) as total_margin,
    AVG(margin) as avg_margin
FROM orders
WHERE payment_status = 'paid'
GROUP BY DATE(created_at);

-- Vue pour produits populaires
CREATE OR REPLACE VIEW popular_products AS
SELECT 
    p.id,
    p.name,
    p.selling_price,
    COUNT(oi.id) as times_ordered,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.margin) as total_margin
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id
WHERE o.payment_status = 'paid'
GROUP BY p.id, p.name, p.selling_price
ORDER BY times_ordered DESC;

-- ============================================
-- DONNÉES DE TEST (OPTIONNEL)
-- ============================================

-- Insertion d'un fournisseur de test
-- INSERT INTO suppliers (name, api_type, wallet_balance) 
-- VALUES ('AliExpress Test', 'aliexpress', 1000.00);

-- ============================================
-- FIN DES MIGRATIONS
-- ============================================

