<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f3f4ed">
    <title><?php echo $action === 'cart' ? 'Giỏ hàng' : ($action === 'checkout' ? 'Thanh toán' : ($action === 'success' ? 'Đặt hàng thành công' : 'Ngày Mới | Cửa hàng tiện ích')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/store.css">
    <script src="assets/js/store.js" defer></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="./" aria-label="Ngày Mới - trang chủ">
        <span class="brand-mark">NM</span>
        <span class="brand-copy"><strong>ngày mới</strong><small>ĐỒ TỐT, MỖI NGÀY</small></span>
    </a>
    <nav class="main-nav" aria-label="Điều hướng chính">
        <a href="./#san-pham">Sản phẩm</a>
        <a href="./#ve-chung-toi">Về chúng tôi</a>
    </nav>
    <a class="cart-link" href="?action=cart" aria-label="Giỏ hàng, <?php echo (int) $cartCount; ?> sản phẩm">
        <span class="cart-icon" aria-hidden="true">▱</span>
        <span>Giỏ hàng</span>
        <span class="cart-count"><?php echo (int) $cartCount; ?></span>
    </a>
</header>

<main>
    <?php if ($databaseMessage): ?>
        <div class="system-notice" role="status"><strong>Chế độ xem trước</strong><span><?php echo e($databaseMessage); ?></span></div>
    <?php endif; ?>
    <?php if ($notice): ?>
        <div class="flash-message" role="status"><?php echo e($notice); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="flash-message flash-error" role="alert"><?php echo e($error); ?></div>
    <?php endif; ?>

    <?php if ($action === 'success'): ?>
        <section class="confirmation page-width">
            <div class="confirmation-icon" aria-hidden="true">✓</div>
            <p class="eyebrow">ĐẶT HÀNG THÀNH CÔNG</p>
            <h1>Cảm ơn bạn đã chọn<br>Ngày Mới.</h1>
            <p>Đơn hàng <strong>#<?php echo (int) $order['id']; ?></strong> đã được ghi nhận. Chúng tôi sẽ liên hệ xác nhận trước khi giao.</p>
            <div class="confirmation-total"><span>Tổng thanh toán khi nhận hàng</span><strong><?php echo e(format_price($order['total'])); ?></strong></div>
            <a class="button button-primary" href="./">Tiếp tục mua sắm <span aria-hidden="true">→</span></a>
        </section>

    <?php elseif ($action === 'cart'): ?>
        <section class="page-width shopping-page">
            <div class="page-heading">
                <div><p class="eyebrow">ĐƠN HÀNG CỦA BẠN</p><h1>Giỏ hàng</h1></div>
                <a class="text-link" href="./#san-pham">← Tiếp tục mua sắm</a>
            </div>
            <?php if (!$cartItems): ?>
                <div class="empty-state"><span aria-hidden="true">＋</span><h2>Giỏ hàng đang trống</h2><p>Chọn vài món bạn thích, chúng sẽ nằm ở đây.</p><a class="button button-primary" href="./#san-pham">Khám phá sản phẩm</a></div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-list">
                        <?php foreach ($cartItems as $item): ?>
                            <article class="cart-item">
                                <img src="<?php echo e($item['image_path']); ?>" alt="<?php echo e($item['name']); ?>">
                                <div class="cart-item-info"><span class="product-category">CÒN <?php echo (int) $item['stock']; ?> SẢN PHẨM</span><h2><?php echo e($item['name']); ?></h2><strong><?php echo e(format_price($item['price'])); ?></strong></div>
                                <form class="quantity-form" method="post" action="?action=cart">
                                    <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>">
                                    <input type="hidden" name="operation" value="update">
                                    <input type="hidden" name="product_id" value="<?php echo (int) $item['id']; ?>">
                                    <label for="qty-<?php echo (int) $item['id']; ?>">Số lượng</label>
                                    <input id="qty-<?php echo (int) $item['id']; ?>" type="number" name="quantity" min="0" max="99" value="<?php echo (int) $item['quantity']; ?>">
                                    <button class="button button-small" type="submit">Cập nhật</button>
                                </form>
                                <div class="cart-line-total"><strong><?php echo e(format_price($item['line_total'])); ?></strong><form method="post" action="?action=cart"><input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>"><input type="hidden" name="operation" value="remove"><input type="hidden" name="product_id" value="<?php echo (int) $item['id']; ?>"><button class="remove-button" type="submit">Xóa</button></form></div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <aside class="summary-panel">
                        <p class="eyebrow">TÓM TẮT ĐƠN HÀNG</p><div class="summary-row"><span>Tạm tính</span><strong><?php echo e(format_price($cartTotal)); ?></strong></div><div class="summary-row"><span>Phí giao hàng</span><strong>Miễn phí</strong></div><div class="summary-total"><span>Tổng cộng</span><strong><?php echo e(format_price($cartTotal)); ?></strong></div>
                        <?php if ($databaseReady): ?><a class="button button-primary button-wide" href="?action=checkout">Tiến hành đặt hàng <span aria-hidden="true">→</span></a><?php endif; ?>
                        <small>Thanh toán khi nhận hàng</small>
                    </aside>
                </div>
            <?php endif; ?>
        </section>

    <?php elseif ($action === 'checkout'): ?>
        <section class="page-width shopping-page">
            <div class="page-heading"><div><p class="eyebrow">BƯỚC CUỐI CÙNG</p><h1>Thông tin giao hàng</h1></div><a class="text-link" href="?action=cart">← Quay lại giỏ hàng</a></div>
            <?php if (!$cartItems): ?>
                <div class="empty-state"><h2>Giỏ hàng đang trống</h2><a class="button button-primary" href="./#san-pham">Chọn sản phẩm</a></div>
            <?php else: ?>
                <div class="checkout-layout">
                    <form class="checkout-form" method="post" action="?action=checkout">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>"><input type="hidden" name="operation" value="checkout">
                        <label for="customer_name">Họ và tên</label><input id="customer_name" name="customer_name" autocomplete="name" maxlength="160" required value="<?php echo e($form['customer_name']); ?>" placeholder="Tên người nhận">
                        <label for="phone">Số điện thoại</label><input id="phone" name="phone" type="tel" autocomplete="tel" maxlength="30" required value="<?php echo e($form['phone']); ?>" placeholder="09xx xxx xxx">
                        <label for="address">Địa chỉ nhận hàng</label><textarea id="address" name="address" autocomplete="street-address" maxlength="500" rows="4" required placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"><?php echo e($form['address']); ?></textarea>
                        <div class="payment-note"><span aria-hidden="true">✓</span><div><strong>Thanh toán khi nhận hàng</strong><small>Bạn thanh toán trực tiếp cho nhân viên giao hàng.</small></div></div>
                        <button class="button button-primary button-wide" type="submit">Đặt hàng · <?php echo e(format_price($cartTotal)); ?> <span aria-hidden="true">→</span></button>
                    </form>
                    <aside class="summary-panel checkout-summary"><p class="eyebrow">ĐƠN HÀNG CỦA BẠN</p>
                        <?php foreach ($cartItems as $item): ?><div class="checkout-item"><span class="checkout-quantity"><?php echo (int) $item['quantity']; ?></span><span><?php echo e($item['name']); ?></span><strong><?php echo e(format_price($item['line_total'])); ?></strong></div><?php endforeach; ?>
                        <div class="summary-row"><span>Giao hàng</span><strong>Miễn phí</strong></div><div class="summary-total"><span>Tổng cộng</span><strong><?php echo e(format_price($cartTotal)); ?></strong></div>
                    </aside>
                </div>
            <?php endif; ?>
        </section>

    <?php else: ?>
        <section class="hero page-width">
            <div class="hero-copy"><p class="eyebrow"><span class="eyebrow-dot"></span> NHỮNG MÓN ĐỒ ĐÁNG DÙNG</p><h1>Chọn điều tốt.<br><em>Dùng mỗi ngày.</em></h1><p class="hero-description">Đồ dùng tinh gọn, hữu ích và được chọn kỹ để mỗi ngày của bạn nhẹ nhàng hơn.</p><a class="button button-dark" href="#san-pham">Khám phá bộ sưu tập <span aria-hidden="true">↓</span></a><div class="hero-footnote"><span class="hero-avatars" aria-hidden="true"><i>N</i><i>M</i><i>+</i></span><span>Món hay cho nhịp sống thường ngày</span></div></div>
            <div class="hero-art" aria-label="Một số món đồ được tuyển chọn">
                <div class="hero-image-main"><img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=90" alt="Tai nghe chụp tai màu cam"></div>
                <div class="hero-sticker"><span>ĐẸP<br>VÀ DÙNG<br>ĐƯỢC LÂU</span><b aria-hidden="true">✳</b></div>
                <div class="hero-image-small"><img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=500&q=85" alt="Đồng hồ đeo tay tối giản"></div>
                <span class="hero-index">BỘ SƯU TẬP 01 / 06</span>
            </div>
        </section>

        <section class="benefits-strip" id="ve-chung-toi"><div><span aria-hidden="true">✳</span> Chọn lọc kỹ càng</div><div><span aria-hidden="true">↗</span> Giao hàng toàn quốc</div><div><span aria-hidden="true">♡</span> Hỗ trợ tận tâm</div><div><span aria-hidden="true">₫</span> Thanh toán khi nhận</div></section>

        <section class="catalog page-width" id="san-pham">
            <div class="catalog-heading"><div><p class="eyebrow">GÓC ĐỒ HAY</p><h2>Món bạn sẽ thích<span>.</span></h2></div><label class="search-box"><span aria-hidden="true">⌕</span><input id="product-search" type="search" placeholder="Tìm món đồ..." aria-label="Tìm sản phẩm"></label></div>
            <div class="catalog-tools"><div class="category-list" role="group" aria-label="Lọc theo danh mục"><button class="category-chip is-active" type="button" data-category="all">Tất cả</button>
                <?php $categories = array_unique(array_column($products, 'category')); foreach ($categories as $category): ?><button class="category-chip" type="button" data-category="<?php echo e($category); ?>"><?php echo e($category); ?></button><?php endforeach; ?>
            </div><span class="product-count"><?php echo count($products); ?> sản phẩm</span></div>
            <div class="product-grid" id="product-grid">
                <?php foreach ($products as $index => $product): ?>
                    <article class="product-card" data-category="<?php echo e($product['category']); ?>" data-search="<?php echo e($product['name'] . ' ' . $product['description'] . ' ' . $product['category']); ?>">
                        <div class="product-image product-tone-<?php echo (int) ($index % 4); ?>"><img src="<?php echo e($product['image_path']); ?>" alt="<?php echo e($product['name']); ?>" loading="lazy"><span class="product-number">0<?php echo (int) ($index + 1); ?></span></div>
                        <div class="product-details"><div class="product-meta"><span class="product-category"><?php echo e($product['category']); ?></span><span class="stock-label"><?php echo (int) $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng'; ?></span></div><h3><?php echo e($product['name']); ?></h3><p><?php echo e($product['description']); ?></p><div class="product-buy"><strong><?php echo e(format_price($product['price'])); ?></strong>
                            <?php if ($databaseReady && (int) $product['stock'] > 0): ?><form method="post" action="?action=store"><input type="hidden" name="csrf_token" value="<?php echo e($csrfToken); ?>"><input type="hidden" name="operation" value="add"><input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>"><button class="add-button" type="submit" aria-label="Thêm <?php echo e($product['name']); ?> vào giỏ">＋</button></form><?php else: ?><button class="add-button" type="button" disabled aria-label="Chưa thể thêm sản phẩm">＋</button><?php endif; ?>
                        </div></div>
                    </article>
                <?php endforeach; ?>
                <p class="no-results" id="no-results" hidden>Không tìm thấy sản phẩm phù hợp.</p>
            </div>
        </section>
        <section class="closing-band"><div class="page-width closing-inner"><p class="eyebrow">NGÀY MỚI, ĐỒ TỐT</p><h2>Ít hơn, nhưng<br><em>đúng thứ bạn cần.</em></h2><a class="button button-light" href="#san-pham">Xem sản phẩm <span aria-hidden="true">↑</span></a></div></section>
    <?php endif; ?>
</main>

<footer class="site-footer"><a class="brand brand-footer" href="./"><span class="brand-mark">NM</span><span class="brand-copy"><strong>ngày mới</strong><small>ĐỒ TỐT, MỖI NGÀY</small></span></a><span>© <?php echo date('Y'); ?> Ngày Mới. Chọn điều tốt, dùng mỗi ngày.</span><a href="mailto:hello@ngaymoi.local">Liên hệ cửa hàng ↗</a></footer>
</body>
</html>