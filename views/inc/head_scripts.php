<link href="assets/template/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
  href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
  rel="stylesheet" />
<!-- Icons -->
<link rel="stylesheet" href="assets/vendor/fonts/fontawesome.css" />
<link rel="stylesheet" href="assets/vendor/fonts/tabler-icons.css" />
<link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css" />
<link rel="stylesheet" type="text/css" href="assets/template/dist/css/uicons-regular-rounded.css" />
<link href="assets/template/dist/css/style.min.css" rel="stylesheet">
<link href="assets/customClassPagination.css" rel="stylesheet">
<link href="assets/css/scroll-menu.css" rel="stylesheet">
<link href="assets/css/custom.css" rel="stylesheet">
<style>
/* Force white sidebar */
.left-sidebar { background: #ffffff !important; }
.left-sidebar .scroll-sidebar { background: #ffffff !important; }
.left-sidebar .sidebar-nav { background: #ffffff !important; }
.left-sidebar .sidebar-nav ul,
.left-sidebar .sidebar-nav ul li { background: #ffffff !important; }
.sidebar-nav #sidebarnav .sidebar-item .sidebar-link,
.sidebar-nav #sidebarnav > li > a { color: #212529 !important; }
.sidebar-nav #sidebarnav .sidebar-item .sidebar-link i,
.sidebar-nav #sidebarnav > li > a i { color: #212529 !important; }
.sidebar-nav #sidebarnav .sidebar-item .sidebar-link:hover,
.sidebar-nav #sidebarnav .sidebar-item.active > .sidebar-link,
.sidebar-nav #sidebarnav > li > a:hover,
.sidebar-nav #sidebarnav > li.active > a { color: #467FFF !important; }
.sidebar-nav #sidebarnav .sidebar-item .sidebar-link:hover i,
.sidebar-nav #sidebarnav .sidebar-item.active > .sidebar-link i { color: #467FFF !important; }
.user-profile { background: #ffffff !important; }
.user-profile .user-name, .user-profile h5 { color: #3c4858 !important; }

/* Compact topbar profile dropdown */
.topbar .user-dd { min-width: 200px !important; }
.topbar .user-dd .d-flex.no-block.align-items-center { padding: 10px 12px !important; }
.topbar .user-dd .d-flex.no-block.align-items-center img { width: 40px !important; height: 40px !important; }
.topbar .user-dd .d-flex.no-block.align-items-center h4 { font-size: 13px !important; margin-bottom: 1px !important; }
.topbar .user-dd .d-flex.no-block.align-items-center p { font-size: 11px !important; margin-bottom: 0 !important; }
.topbar .user-dd .dropdown-item { padding: 6px 14px !important; font-size: 13px !important; }
.topbar .user-dd .dropdown-item i { font-size: 12px !important; }

/* Create new shipment button - blue instead of red */
.create-btn {
    background-color: #467FFF !important;
    background-image: none !important;
    border-color: #467FFF !important;
}
.create-btn:hover {
    background-color: #2f6fe0 !important;
    border-color: #2f6fe0 !important;
}

/* SETTINGS nav-small-cap - white background, dark text */
.left-sidebar .nav-small-cap {
    background: #ffffff !important;
    color: #3c4858 !important;
}
.left-sidebar .nav-small-cap span,
.left-sidebar .nav-small-cap i {
    color: #3c4858 !important;
}

/* Remove black divider lines in sidebar */
.left-sidebar .sidebar-nav ul li,
.left-sidebar .sidebar-nav ul,
.left-sidebar .scroll-sidebar,
.left-sidebar,
.sidebar-nav #sidebarnav .sidebar-item,
.sidebar-nav #sidebarnav .nav-small-cap {
    border-color: transparent !important;
    border-top: none !important;
    border-bottom: none !important;
}

/* Remove border on user-profile and nav-small-cap dividers */
.left-sidebar .user-profile,
.left-sidebar .nav-small-cap,
.left-sidebar #sidebarnav > li,
.left-sidebar #sidebarnav li {
    border: none !important;
    border-bottom: none !important;
    border-top: none !important;
    box-shadow: none !important;
}

/* Remove spacing above and below create-btn */
.left-sidebar #sidebarnav > li.p-15 {
    padding-top: 8px !important;
    padding-bottom: 8px !important;
    margin-top: 0 !important;
}

/* Collapse empty nav-small-cap gaps */
.left-sidebar #sidebarnav > li.nav-small-cap {
    padding: 0 !important;
    margin: 0 !important;
    min-height: 0 !important;
    height: 0 !important;
    overflow: hidden !important;
}

/* Remove the dark border-bottom on the li wrapping user-profile and create-btn */
.left-sidebar #sidebarnav > li:first-child,
.left-sidebar #sidebarnav > li.p-15,
.left-sidebar #sidebarnav > li:has(.user-profile),
.left-sidebar #sidebarnav > li:has(.create-btn) {
    border-bottom: none !important;
    border-top: none !important;
    border: none !important;
}

/* Target skin1 border specifically */
#main-wrapper .left-sidebar #sidebarnav li,
#main-wrapper .left-sidebar #sidebarnav > li {
    border-bottom: none !important;
    border-top: none !important;
}

/* Fix create-btn text color to white */
.create-btn,
.create-btn span,
.create-btn i,
.create-btn * {
    color: #ffffff !important;
}

/* Override sidebar icon color rule specifically for create-btn */
#sidebarnav .create-btn i,
#sidebarnav li .create-btn i,
.sidebar-nav #sidebarnav li .create-btn i {
    color: #ffffff !important;
}

/* Sidebar arrow chevrons - dark instead of red/orange */
.sidebar-nav .has-arrow::after {
    border-color: #212529 !important;
}
.sidebar-nav .has-arrow:hover::after,
.sidebar-nav .sidebar-item.active > .has-arrow::after {
    border-color: #467FFF !important;
}

/* Form inputs - blue border on focus instead of red */
.form-control:focus,
.form-control:active,
input:focus,
input:active,
select:focus,
select:active,
textarea:focus,
textarea:active {
    border-color: #467FFF !important;
    box-shadow: 0 0 0 0.2rem rgba(70, 127, 255, 0.25) !important;
    outline: none !important;
}

/* Override hardcoded red inline borders on inputs */
input.form-control[style*="border"],
select.form-control[style*="border"],
select.custom-select[style*="border"],
input[style*="border: 1px solid red"],
select[style*="border: 1px solid red"],
textarea[style*="border: 1px solid red"] {
    border: 1px solid #467FFF !important;
}

/* Override purple primary color to blue #467FFF */
.text-primary { color: #467FFF !important; }
.btn-primary {
    background-color: #467FFF !important;
    border-color: #467FFF !important;
    color: #ffffff !important;
}
.btn-primary:hover {
    background-color: #2f6fe0 !important;
    border-color: #2f6fe0 !important;
}
.bg-primary { background-color: #467FFF !important; }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />


<?php
if ($direction_layout == 'rtl') {
?>
    <link href="https://fonts.googleapis.com/css?family=Tajawal&subset=arabic" rel="stylesheet">
    <style>
        * {
            font-family: 'Tajawal';
        }
    </style>
<?php
}
?>