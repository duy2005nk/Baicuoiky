<?php

class StoreController
{
    private Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function handle(): array
    {
        $action = (string) ($_GET['action'] ?? 'store');
        $error = null;
        $form = ['customer_name' => '', 'phone' => '', 'address' => ''];
        $notice = $_SESSION['notice'] ?? null;
        unset($_SESSION['notice']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = (string) ($_POST['operation'] ?? 'store');
            $submittedToken = (string) ($_POST['csrf_token'] ?? '');
            $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');

            if ($sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
                $error = 'Phien lam viec da het han. Hay tai lai trang va thu lai.';
            } else {
                try {
                    $this->handlePost($action, $form, $error);
                } catch (DomainException $exception) {
                    $error = $exception->getMessage();
                    $action = $action === 'checkout' ? 'checkout' : (in_array($action, ['update', 'remove'], true) ? 'cart' : 'store');
                }
            }
        }

        if ($action === 'success' && isset($_SESSION['last_order'])) {
            return [
                'action' => 'success',
                'order' => $_SESSION['last_order'],
                'csrfToken' => $_SESSION['csrf_token'],
                'cartCount' => 0,
                'error' => $error,
                'notice' => $notice,
            ];
        }

        if (!in_array($action, ['store', 'cart', 'checkout'], true)) {
            $action = 'store';
        }

        $cart = $_SESSION['cart'] ?? [];
        $cartItems = $this->store->cartItems($cart);
        $cartCount = array_sum(array_map(function ($item) {
            return (int) $item['quantity'];
        }, $cartItems));

        return [
            'action' => $action,
            'products' => $this->store->products(),
            'cartItems' => $cartItems,
            'cartCount' => $cartCount,
            'cartTotal' => array_sum(array_column($cartItems, 'line_total')),
            'csrfToken' => $_SESSION['csrf_token'],
            'error' => $error,
            'notice' => $notice,
            'form' => $form,
        ];
    }

    private function handlePost(string $action, array &$form, ?string &$error): void
    {
        if ($action === 'add') {
            $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            $product = $id ? $this->store->findProduct((int) $id) : null;

            if (!$product || (int) $product['is_active'] !== 1 || (int) $product['stock'] < 1) {
                throw new DomainException('San pham hien khong con hang.');
            }

            $cart = $_SESSION['cart'] ?? [];
            $cart[(int) $id] = min((int) $product['stock'], (int) ($cart[(int) $id] ?? 0) + 1);
            $_SESSION['cart'] = $cart;
            $_SESSION['notice'] = 'Da them san pham vao gio hang.';
            $this->redirect('store');
        }

        if ($action === 'update') {
            $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
            $cart = $_SESSION['cart'] ?? [];

            if (!$id || !isset($cart[(int) $id]) || $quantity === false || $quantity === null || $quantity < 0 || $quantity > 99) {
                throw new DomainException('So luong cap nhat khong hop le.');
            }

            if ($quantity === 0) {
                unset($cart[(int) $id]);
            } else {
                $cart[(int) $id] = $quantity;
            }

            $_SESSION['cart'] = $cart;
            $_SESSION['notice'] = 'Gio hang da duoc cap nhat.';
            $this->redirect('cart');
        }

        if ($action === 'remove') {
            $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
            $cart = $_SESSION['cart'] ?? [];
            unset($cart[(int) $id]);
            $_SESSION['cart'] = $cart;
            $_SESSION['notice'] = 'Da xoa san pham khoi gio hang.';
            $this->redirect('cart');
        }

        if ($action === 'checkout') {
            $form = [
                'customer_name' => trim((string) ($_POST['customer_name'] ?? '')),
                'phone' => trim((string) ($_POST['phone'] ?? '')),
                'address' => trim((string) ($_POST['address'] ?? '')),
            ];

            if ($form['customer_name'] === '' || strlen($form['customer_name']) > 160) {
                $error = 'Vui long nhap ho ten (toi da 160 ky tu).';
                return;
            }

            if (!preg_match('/^[0-9+() .-]{8,30}$/', $form['phone'])) {
                $error = 'So dien thoai chua dung dinh dang.';
                return;
            }

            if ($form['address'] === '' || strlen($form['address']) > 500) {
                $error = 'Vui long nhap dia chi giao hang (toi da 500 ky tu).';
                return;
            }

            $order = $this->store->createOrder(
                $_SESSION['cart'] ?? [],
                $form['customer_name'],
                $form['phone'],
                $form['address']
            );
            $_SESSION['cart'] = [];
            $_SESSION['last_order'] = $order;
            $this->redirect('success');
        }

        throw new DomainException('Thao tac khong duoc ho tro.');
    }

    private function redirect(string $action): void
    {
        header('Location: ?action=' . rawurlencode($action), true, 303);
        exit;
    }
}