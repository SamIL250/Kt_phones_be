<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-vertical navbar-expand-lg" style="display:none;">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-home" role="button" data-bs-toggle="collapse" aria-expanded="true" aria-controls="nv-home">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div><span class="nav-link-icon"><span data-feather="pie-chart"></span></span><span class="nav-link-text">Dashboard</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent show" data-bs-parent="#navbarVerticalCollapse" id="nv-home">
                                <li class="collapsed-nav-item-title d-none">Dashboard</li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'index.php' ? "active" : ""  ?>" href="index">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Overview</span></div>
                                    </a><!-- more inner pages-->
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">More</p>
                    <hr class="navbar-vertical-line" /><!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link dropdown-indicator label-1" href="#nv-e-commerce" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="nv-e-commerce">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span class="fas fa-caret-right dropdown-indicator-icon"></span></div><span class="nav-link-icon"><span data-feather="shopping-cart"></span></span><span class="nav-link-text">STORE</span>
                            </div>
                        </a>
                        <div class="parent-wrapper label-1">
                            <ul class="nav collapse parent show" data-bs-parent="#e-commerce" id="nv-admin">
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'products-add.php' ? "active" : ""  ?>" href="products-add">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Add product</span></div>
                                    </a><!-- more inner pages-->
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'products.php' || $current_page == 'product.php' ? "active" : ""  ?>" href="products">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Products</span></div>
                                    </a><!-- more inner pages-->
                                </li>
                                <li class="nav-item"><a class="nav-link" href="orders">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Orders</span></div>
                                    </a><!-- more inner pages-->
                                </li>
                                <!-- Management Section -->
                                <p class="navbar-vertical-label mt-3">Management</p>
                                <hr class="navbar-vertical-line" />
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $current_page == 'product-categories.php' || $current_page == 'product-categories.php' ? "active" : ""  ?>" href="product-categories">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Categories</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'product-brands.php' || $current_page == 'product-brands.php' ? "active" : ""  ?>" href="product-brands">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Brands</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'product-attributes.php' || $current_page == 'product-attributes.php' ? "active" : ""  ?>" href="product-attributes">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Attributes</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'product-tags.php' || $current_page == 'product-tags.php' ? "active" : ""  ?>" href="product-tags">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Tags</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'customers.php' || $current_page == 'customers.php' ? "active" : ""  ?>" href="customers">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Customers</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'customers-wishlists.php' || $current_page == 'customers-wishlists.php' ? "active" : ""  ?>" href="customers-wishlists">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Wishlists</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link <?php echo $current_page == 'customers-reviews.php' || $current_page == 'customers-reviews.php' ? "active" : ""  ?>" href="customers-reviews">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Reviews</span></div>
                                    </a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="reports">
                                        <div class="d-flex align-items-center"><span class="nav-link-text">Reports</span></div>
                                    </a>
                                </li>
                            </ul>
                             
                        </div>
                    </div>


                </li>
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer"><button class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span class="fas fa-caret-right dropdown-indicator-icon fs-8"></span><span class="navbar-vertical-footer-text ms-2">Collapsed View</span></button></div>
</nav>