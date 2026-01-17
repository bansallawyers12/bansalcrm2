@extends('layouts.admin')
@section('title', 'Client Detail')

@section('content')
<style>
/* Modern Design CSS Variables */
:root {
	--primary-color: #6366f1;
	--primary-hover: #4f46e5;
	--secondary-color: #06b6d4;
	--success-color: #10b981;
	--background-color: #f8fafc;
	--card-background: #ffffff;
	--text-primary: #0f172a;
	--text-secondary: #64748b;
	--border-color: #e2e8f0;
	--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
	--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
	--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
	--radius-sm: 6px;
	--radius-md: 10px;
	--radius-lg: 16px;
	--radius-full: 9999px;
}

/* Client Detail Container - Pure Flexbox Layout */
.client-detail-container {
	display: flex;
	flex-direction: column;
	gap: 24px;
	width: 100%;
	align-items: flex-start;
}

/* Bottom Row Container - Holds Personal Details and Third Section */
.bottom-row-container {
	display: flex;
	flex-direction: row;
	gap: 24px;
	width: 100%;
	align-items: stretch;
}

/* Personal Details Container - Fixed Width */
.personal-details-container {
	width: 280px;
	min-width: 280px;
	max-width: 280px;
	flex-shrink: 0;
}

/* Right Section - Takes Remaining Space */
.right_section {
	flex: 1;
	min-width: 0;
}

/* Ensure cards inside containers don't overflow */
.personal-details-container .card {
	margin-left: 0 !important;
	margin-right: 0 !important;
}

/* Modern Profile Card Styles */
.author-box.left_section_upper {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: var(--radius-lg);
	box-shadow: var(--shadow-lg);
	border: none;
	position: relative;
	overflow: hidden;
	margin-bottom: 0;
	margin-left: 0 !important;
	margin-right: 0 !important;
}

.author-box.left_section_upper::before {
	content: '';
	position: absolute;
	top: -50%;
	right: -50%;
	width: 200px;
	height: 200px;
	background: rgba(255, 255, 255, 0.1);
	border-radius: 50%;
}

.author-box.left_section_upper .card-body {
	padding: 14px;
	color: white;
	position: relative;
	z-index: 1;
}

.author-box-center {
	display: flex;
	flex-direction: column;
	align-items: center;
	margin-bottom: 12px;
}

.author-avtar {
	width: 55px !important;
	height: 55px !important;
	border-radius: var(--radius-full) !important;
	background: linear-gradient(135deg, var(--secondary-color), #0891b2) !important;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 22px !important;
	font-weight: 700 !important;
	color: white !important;
	margin-bottom: 8px;
	box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
	border: 2px solid rgba(255, 255, 255, 0.2) !important;
}

.author-box-name {
	text-align: center;
	margin-bottom: 12px;
}

.author-box-name a {
	color: white !important;
	font-size: 17px;
	font-weight: 700;
	text-decoration: none;
}

.author-box-name span {
	color: rgba(255, 255, 255, 0.9);
	font-size: 12px;
	font-weight: 500;
	letter-spacing: 0.5px;
	display: block;
	margin-top: 4px;
}

.author-mail_sms {
	display: flex;
	gap: 8px;
	justify-content: center;
	margin: 12px 0;
	flex-wrap: wrap;
}

.author-mail_sms > a,
.author-mail_sms > span {
	width: 28px;
	height: 28px;
	border-radius: var(--radius-full);
	background: rgba(255, 255, 255, 0.15);
	backdrop-filter: blur(10px);
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s ease;
	border: 1px solid rgba(255, 255, 255, 0.2);
	color: white !important;
	text-decoration: none;
}

.author-mail_sms > a:hover {
	background: rgba(255, 255, 255, 0.25);
	transform: translateY(-2px);
}

.author-mail_sms > a i {
	font-size: 12px;
	color: white;
}

.author-box.left_section_upper p:has(.badge-outline) {
	display: flex;
	gap: 8px;
	justify-content: center;
	margin-bottom: 12px;
	flex-wrap: wrap;
}

.author-box.left_section_upper .badge-outline,
.author-box.left_section_upper p a.badge-outline {
	padding: 5px 12px;
	border-radius: var(--radius-full);
	font-size: 11px;
	font-weight: 600;
	backdrop-filter: blur(10px);
	border: 1px solid rgba(255, 255, 255, 0.2) !important;
	background: rgba(255, 255, 255, 0.2) !important;
	color: white !important;
	text-decoration: none;
}

.author-box.left_section_upper .badge-outline.active,
.author-box.left_section_upper p a.badge-outline.active {
	background: var(--success-color) !important;
	color: white !important;
	box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.author-box.left_section_upper .btn-primary.btn-block {
	width: 100%;
	padding: 10px;
	background: rgba(255, 255, 255, 0.95);
	color: var(--primary-color);
	border: none;
	border-radius: var(--radius-md);
	font-weight: 600;
	font-size: 13px;
	cursor: pointer;
	transition: all 0.3s ease;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.author-box.left_section_upper .btn-primary.btn-block:hover {
	background: white;
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

/* Modern Personal Details Card */
.card.left_section_lower {
	background: var(--card-background);
	border-radius: var(--radius-lg);
	box-shadow: var(--shadow-sm);
	border: 1px solid var(--border-color);
	margin-left: 0 !important;
	margin-right: 0 !important;
}

.card.left_section_lower .card-header {
	background: transparent;
	border-bottom: 2px solid var(--background-color);
	padding: 12px 16px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	flex-wrap: wrap;
}

.card.left_section_lower .card-header h4 {
	font-size: 16px;
	font-weight: 700;
	color: var(--text-primary);
	margin: 0;
	display: flex;
	align-items: center;
	gap: 8px;
}

.card.left_section_lower .card-body {
	padding: 14px 18px;
}

.card.left_section_lower .card-body p.clearfix {
	display: flex;
	flex-direction: column;
	gap: 4px;
	margin-bottom: 10px;
	padding-bottom: 10px;
	border-bottom: 1px solid var(--background-color);
	line-height: 1.5;
}

.card.left_section_lower .card-body p.clearfix:last-child {
	border-bottom: none;
	margin-bottom: 0;
	padding-bottom: 0;
}

.card.left_section_lower .card-body .float-start {
	font-size: 11px;
	color: var(--text-secondary);
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.3px;
	line-height: 1.4;
}

.card.left_section_lower .card-body .float-end {
	font-size: 13px;
	color: var(--text-primary);
	font-weight: 500;
	word-break: break-word;
	line-height: 1.5;
}

.add_note {
	display: flex;
	gap: 8px;
	align-items: center;
}

.add_note .not_picked_call,
.add_note .create_note_d {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 3px 10px;
	border-radius: var(--radius-sm);
	font-size: 10px;
	font-weight: 600;
	letter-spacing: 0.5px;
	border: none;
}

.add_note .create_note_d {
	font-size: 12px;
	color: var(--primary-color);
	background: transparent;
	padding: 0;
	cursor: pointer;
	display: flex;
	align-items: center;
	gap: 6px;
	font-weight: 500;
	transition: all 0.2s ease;
}

.add_note .create_note_d:hover {
	color: var(--primary-hover);
	transform: translateX(2px);
}

/* Modern Tab Navigation */
.card.right_section .nav-pills {
	background: var(--background-color);
	padding: 12px 16px;
	border-bottom: 1px solid var(--border-color);
	gap: 4px;
	display: flex;
	flex-wrap: wrap;
}

.card.right_section .nav-pills .nav-link {
	padding: 10px 20px;
	background: transparent;
	border: none;
	color: var(--text-secondary);
	font-weight: 500;
	font-size: 14px;
	border-radius: var(--radius-md);
	transition: all 0.3s ease;
	white-space: nowrap;
}

.card.right_section .nav-pills .nav-link:hover {
	background: rgba(99, 102, 241, 0.08);
	color: var(--primary-color);
}

.card.right_section .nav-pills .nav-link.active {
	background: var(--primary-color);
	color: white;
	box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Modern Filter Section */
.activities-filter-bar {
	background: var(--background-color);
	padding: 24px;
	border-radius: var(--radius-md);
	margin-bottom: 28px;
	border: 1px solid var(--border-color);
}

.activities-filter-bar .form-control {
	padding: 10px 16px;
	border: 1.5px solid var(--border-color);
	border-radius: var(--radius-md);
	font-size: 14px;
	transition: all 0.3s ease;
	background: white;
}

.activities-filter-bar .form-control:focus {
	outline: none;
	border-color: var(--primary-color);
	box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.activities-filter-bar .activity-type-btn {
	border: 1.5px solid var(--border-color);
	background: white;
	border-radius: var(--radius-md);
	font-size: 13px;
	font-weight: 500;
	transition: all 0.3s ease;
	color: var(--text-secondary);
}

.activities-filter-bar .activity-type-btn:hover {
	border-color: var(--primary-color);
	color: var(--primary-color);
}

.activities-filter-bar .activity-type-btn.active {
	background: var(--primary-color);
	color: white;
	border-color: var(--primary-color);
}

.activities-filter-bar .btn-primary {
	background: var(--primary-color);
	color: white;
	box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
	border-radius: var(--radius-md);
	padding: 10px 24px;
	font-weight: 600;
	font-size: 13px;
	border: none;
	transition: all 0.3s ease;
}

.activities-filter-bar .btn-primary:hover {
	background: var(--primary-hover);
	transform: translateY(-2px);
	box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.activities-filter-bar .btn-secondary {
	background: white;
	color: var(--text-secondary);
	border: 1.5px solid var(--border-color);
	border-radius: var(--radius-md);
	padding: 10px 24px;
	font-weight: 600;
	font-size: 13px;
}

.activities-filter-bar .btn-secondary:hover {
	background: var(--background-color);
	border-color: var(--text-secondary);
}

/* Modern Timeline/Activities */
.activity {
	position: relative;
	padding-left: 60px;
	margin-bottom: 24px;
}

.activity::before {
	content: '';
	position: absolute;
	left: 14px;
	top: 0;
	bottom: -24px;
	width: 2px;
	background: linear-gradient(180deg, var(--primary-color) 0%, rgba(99, 102, 241, 0.1) 100%);
}

.activity:last-child::before {
	display: none;
}

.activity-icon {
	position: absolute;
	left: 0;
	width: 32px;
	height: 32px;
	border-radius: var(--radius-full);
	background: var(--primary-color);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-weight: 700;
	font-size: 14px;
	box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
	border: 3px solid var(--card-background);
	z-index: 1;
}

.activity-detail {
	background: white !important;
	border-radius: var(--radius-md);
	padding: 20px;
	box-shadow: var(--shadow-sm);
	border: 1px solid var(--border-color) !important;
	transition: all 0.3s ease;
}

.activity-detail:hover {
	box-shadow: var(--shadow-md);
	transform: translateX(4px);
}

.activity-head {
	display: flex;
	justify-content: space-between;
	align-items: start;
	margin-bottom: 12px;
}

.activity-title {
	font-size: 15px;
	color: var(--text-primary);
	font-weight: 600;
	flex: 1;
	line-height: 1.5;
}

.activity-title b {
	color: var(--primary-color);
	font-weight: 700;
}

.activity-date {
	font-size: 13px;
	color: var(--text-secondary);
	display: flex;
	align-items: center;
	gap: 6px;
}

.activity-detail p {
	color: var(--text-secondary);
	font-size: 14px;
	line-height: 1.6;
	margin-top: 8px;
	margin-bottom: 0;
}

.verified-icon {
	color: var(--success-color);
	font-size: 14px;
}

.unverified-icon {
	color: var(--text-secondary);
	font-size: 14px;
}

.popover {max-width:700px;}
.ag-space-between {justify-content: space-between;}
.ag-align-center {align-items: center;}
.ag-flex {display: flex;}
.ag-align-start {align-items: flex-start;}
.ag-flex-column {flex-direction: column;}
.col-hr-1 {margin-right: 5px!important;}
.text-semi-bold {font-weight: 600!important;}
.small, small {font-size: 85%;}
.ag-align-end { align-items: flex-end;}


.ui.label:last-child {margin-right: 0;}
.ui.label:first-child { margin-left: 0;}
.field .ui.label {padding-left: 0.78571429em; padding-right: 0.78571429em;}
.ag-appointment-list__title{padding-left: 1rem; text-transform: uppercase;}
.zippyLabel{background-color: #e8e8e8; line-height: 1;display: inline-block;color: rgba(0,0,0,.6);font-weight: 700; border: 0 solid transparent; font-size: 10px;padding: 3px; }
.accordion .accordion-header.app_green{background-color: #54b24b;color: #fff;}
.accordion .accordion-header.app_green .accord_hover a{color: #fff!important;}
.accordion .accordion-header.app_blue{background-color: rgba(3,169,244,.1);color: #03a9f4;}
.badge-outline {
    display: inline-block;
    padding: 5px 8px;
    line-height: 12px;
    border: 1px solid;
    border-radius: 0.25rem;
    font-weight: 400;
    font-size: 13px;
}
.col-greenf{color: #9b9f9b !important;}
.badge-outline.col-greenf.active{background: #4caf50 !important;color:#fff!important;}
.badge-outline.col-redf.active{background: #4caf50 !important;color:#fff!important;}
.uploadchecklists .table thead th {
    border-bottom: none;
    background-color: rgba(0,0,0,0.04);
    color: #666;
    padding-top: 15px;
    padding-bottom: 15px;
}
.card .card-body ul.nav-pills li.nav-item {margin: 0px 0px 0px 0px;}

/* Commission Invoice Modal Select2 Dropdown Styles */
#opencommissionmodal .select2-results__options {
	max-height: 300px !important;
	overflow-y: auto !important;
	overflow-x: hidden !important;
}
#opencommissionmodal .select2-search--dropdown {
	position: relative !important;
	z-index: 1 !important;
	display: block !important;
}
#opencommissionmodal .select2-search--dropdown .select2-search__field {
	width: 100% !important;
	padding: 6px !important;
	border: 1px solid #aaa !important;
	border-radius: 4px !important;
}
#opencommissionmodal .select2-container {
	z-index: 9999 !important;
}
#opencommissionmodal .select2-dropdown {
	z-index: 9999 !important;
}

/* Add Application Modal Select2 Dropdown Styles - Fix dropdown appearing below modal */
.add_appliation .select2-container {
	z-index: 9999 !important;
}
.add_appliation .select2-dropdown {
	z-index: 9999 !important;
}
.add_appliation .modal-body {
	overflow: visible !important;
}
.add_appliation .modal-content {
	overflow: visible !important;
}

 .file-preview-container {
    border: 1px solid #ddd;
    padding: 10px;
    min-height: 300px;
    text-align: center;
    display: inline-block;
}

/* Ensure Bootstrap dropdowns work properly in tables */
.table .dropdown {
    position: relative;
}

.table .dropdown-menu {
    z-index: 1050 !important;
    position: absolute !important;
}

/* Fix for dropdowns inside table cells */
td .dropdown-menu {
    z-index: 1050 !important;
}

/* Document row right-click styling */
.document-row {
    cursor: context-menu !important;
    user-select: none;
}

.document-row td {
    cursor: context-menu !important;
}

/* Allow links and buttons inside rows to work normally */
.document-row a[href]:not([href^="javascript:"]),
.document-row button,
.document-row input,
.document-row textarea,
.document-row select {
    cursor: pointer !important;
}

/* Context Menu Styles */
.document-context-menu {
    display: none;
    position: fixed;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 10000;
    min-width: 180px;
    padding: 4px 0;
    list-style: none;
    margin: 0;
}

.document-context-menu.show {
    display: block;
}

.document-context-menu li {
    margin: 0;
    padding: 0;
}

.document-context-menu a {
    display: block;
    padding: 8px 16px;
    color: #333;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
}

.document-context-menu a:hover {
    background-color: #f5f5f5;
}

.document-context-menu a.disabled {
    color: #999;
    cursor: not-allowed;
    pointer-events: none;
}

.document-context-menu .divider {
    height: 1px;
    margin: 4px 0;
    background-color: #e0e0e0;
    padding: 0;
}

.preview-image {
    max-width: 100%;
    height: auto;
    display: block;
    margin: auto;
}

.pdf-viewer, .doc-viewer {
    width: 100%;
    height: 400px;
    border: none;
}

/*////////////////////////////////////////////
    ////// appointment popup css chnages start /////////
    //////////////////////////////////////////// */


.timeslots .timeslot_col.active{/*border:1px solid #0062cc;background-color:#fff;*/background-color: #007bff;color: #FFFFFF;margin: 0px 10px 8px 0px;}
#preloaderbook {
	display:none;
    background: #0d104d;
    background: -webkit-linear-gradient(to right, #0d104d, #28408b);
    background: linear-gradient(to right, #0d104d, #28408b);
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 5000;
}
#preloaderbook .circle-preloader {
    display: block;
    width: 60px;
    height: 60px;
    border: 2px solid rgba(255, 255, 255, 0.5);
    border-bottom-color: #ffffff;
    border-radius: 50%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    animation: spin 2s infinite linear;
}

#loading, #loading_popup{
    width: 100%;
    height: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    display: none;
    opacity: 0.7;
    background-color: #fff;
    z-index: 99;
    text-align: center;
}

#loading-image {
    position: absolute;
    top: 100px;
    left: 600px;
    z-index: 100;
}

#loading-image_popup {
    position: absolute;
    top: 100px;
    left: 100px;
    z-index: 100;
}

.services_item_title_span {
    font-size: 18px;
    line-height: 21px;
    color: #828F9A;
    display: inline-block;
    padding-left: 10px;
}

.services_item_price {
    float: right;
    display: inline-block;
    font-size: 24px;
    line-height: 30px;
    color: #53d56c;
    /* margin-top: 10px; */
}
.services_item_description {
    font-size: 14px;
    /* line-height: 18px; */
    color: #828F9A;
    display: inline-block;
    margin-bottom: 10px;
    margin-left: 25px;
    margin-top: 5px;
}
#datetimepicker {
    max-width: 330px;
    font-size: 14px;
    line-height: 21px;
    margin: 0px auto;
    background: #d3d4ec;
    padding: 8px;
    border-radius: 5px;
}
.timeslots .timeslot_col {
    display: flex;
    flex-direction: column;
    width: calc(33% - 10px);
    float: left;
    background: #d3d4ec;
    padding: 5px;
    margin: 0px 10px 10px 0px;
    text-align: center;
}

/*////////////////////////////////////////////
////// appointment popup css chnages end /////////
//////////////////////////////////////////// */

  .filter_panel {background: #f7f7f7;margin: 10px 10px 10px 10px;border: 1pxsolid #eee;display: none;}
.card .card-body .filter_panel { padding: 20px;}

/* Activities Filter Bar Styles */
.activities-filter-bar {
	background: #f8f9fa;
	padding: 15px;
	border-radius: 5px;
	margin-bottom: 20px;
	border: 1px solid #e0e0e0;
}

.activity-type-btn {
	border: 1px solid #ddd;
	background: #fff;
	padding: 5px 12px;
	font-size: 12px;
	border-radius: 4px;
	white-space: nowrap;
	transition: all 0.2s;
	cursor: pointer;
}

.activity-type-btn:hover {
	background: #f0f0f0;
	border-color: #bbb;
}

.activity-type-btn.active {
	background: #6777ef;
	color: #fff;
	border-color: #6777ef;
	font-weight: 600;
}

.activity-type-btn.active:hover {
	background: #5568d3;
	border-color: #5568d3;
}

.activity-type-btn.dropdown-toggle.active {
	background: #6777ef;
	color: #fff;
	border-color: #6777ef;
}

.activity-type-dropdown-item.active {
	background-color: #6777ef;
	color: #fff;
}

.activity-type-dropdown-item:hover {
	background-color: #f0f0f0;
}

.activity-type-dropdown-item.active:hover {
	background-color: #5568d3;
}

.date-filter {
	font-size: 12px;
}

.btn-assignaction:hover {
    background-color: rgba(255, 255, 255, 0.95) !important;
	color: #fff !important;
}
#assignUser {
	color: #fff !important;
}
/* Bulk Upload Styles */
.bulk-upload-dropzone {
    transition: all 0.3s ease;
}

.bulk-upload-dropzone:hover {
    border-color: #2563eb !important;
    background-color: #eff6ff !important;
}

.bulk-upload-dropzone.drag_over {
    border-color: #10b981 !important;
    background-color: #d1fae5 !important;
    transform: scale(1.02);
}

.bulk-upload-file-item {
    padding: 10px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.bulk-upload-file-item:last-child {
    border-bottom: none;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.file-name {
    font-weight: 500;
    color: #333;
}

.file-size {
    font-size: 12px;
    color: #666;
}

.checklist-select {
    min-width: 200px;
}

.match-status {
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.match-status.auto-matched {
    background: #d1fae5;
    color: #065f46;
}

.match-status.manual {
    background: #fef3c7;
    color: #92400e;
}

.match-status.new-checklist {
    background: #dbeafe;
    color: #1e40af;
}

</style>
<?php
use App\Http\Controllers\Controller;

?>
<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>

			<?php
            //List if any attending inperssion session
            $attendingSessionExist = \App\Models\CheckinLog::where('client_id', '=', $fetchedData->id)->where('status', '=', '2')->orderBy('id', 'DESC')->get();
            if(!empty($attendingSessionExist) && count($attendingSessionExist) >0){?>
                <div class="row mb-3">
                    <div class="col-12">
                        <a href="javascript:void(0);" class="btn btn-primary complete_session" data-clientid="<?php echo $fetchedData->id;?>">Complete Session</a>
                    </div>
                </div>
            <?php }?>

		<div class="client-detail-container">
			<!-- Bottom Row: Personal Details (with Avatar) + Third Section -->
			<div class="bottom-row-container">
				<div class="personal-details-container">
					<div class="card left_section_lower">
						<!-- Avatar Section at Top -->
						<div class="card-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px 16px 0 0; padding: 20px; color: white;">
							<div class="author-box-center">
								<span class="author-avtar"><b>{{substr($fetchedData->first_name, 0, 1)}}{{substr($fetchedData->last_name, 0, 1)}}</b></span>
								<div class="author-box-name">
									<a href="#" style="color: white; text-decoration: none;">{{$fetchedData->first_name}} {{$fetchedData->last_name}}</a>
									<span style="color: rgba(255, 255, 255, 0.9); font-size: 11px;">{{$fetchedData->client_id}}</span>
								</div>
							</div>
								
							<div class="author-mail_sms">
								<a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="sendmsg" title="Send Message"><i class="fas fa-comment-alt"></i></a>
								<a href="javascript:;" data-id="{{@$fetchedData->id}}" data-email="{{@$fetchedData->email}}" data-name="{{@$fetchedData->first_name}} {{@$fetchedData->last_name}}" class="clientemail" title="Compose Mail"><i class="fa fa-envelope"></i></a>
								<a href="{{URL::to('/clients/edit/'.base64_encode(convert_uuencode(@$fetchedData->id)))}}" title="Edit"><i class="fa fa-edit"></i></a>
								@if($fetchedData->is_greview_mail_sent == 0)
									<a class="googleReviewBtn" href="javascript:;" data-is_greview_mail_sent="{{@$fetchedData->is_greview_mail_sent}}" title="Google Review"><i class="fab fa-google"></i></a>
								@endif
								@if($fetchedData->is_archived == 0)
									<a class="arcivedval" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="Archive"><i class="fas fa-archive"></i></a>
								@else
									<a class="arcivedval" style="background-color:rgba(239, 68, 68, 0.8);" href="javascript:;" onclick="arcivedAction({{$fetchedData->id}}, 'admins')" title="UnArchive"><i class="fas fa-archive"></i></a>
								@endif
							</div>
							
							<div style="display: flex; gap: 8px; justify-content: center; margin: 12px 0; flex-wrap: wrap;">
								<a onclick="return confirm('Are you sure?')" class="badge-outline col-greenf <?php if($fetchedData->type == 'client'){ echo 'active'; } ?>" href="{{URL::to('/clients/changetype/'.base64_encode(convert_uuencode($fetchedData->id)).'/client')}}">Client</a>
								<a onclick="return confirm('Are you sure?')" href="{{URL::to('/clients/changetype/'.base64_encode(convert_uuencode($fetchedData->id)).'/lead')}}" class="badge-outline col-greenf <?php if($fetchedData->type == 'lead'){ echo 'active'; } ?>">Lead</a>
							</div>
							
							<button type="button" class="btn btn-primary btn-block btn-assignaction" data-container="body" data-role="popover" data-placement="auto" data-html="true" data-content="<div id=&quot;popover-content&quot;>
									<h4 class=&quot;text-center&quot;>Assign User</h4>
									<div class=&quot;clearfix&quot;></div>

							    <div class=&quot;box-header with-border&quot;>
								    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
										<label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Select Assignee</label>
										<div class=&quot;col-sm-9&quot;>

											<select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;rem_cat&quot; name=&quot;rem_cat&quot; onchange=&quot;&quot;>
												<option value=&quot;&quot; >Select</option>
												@foreach(\App\Models\Admin::select('id', 'office_id', 'first_name', 'last_name')->where('role','!=',7)->where('status',1)->orderby('first_name','ASC')->get() as $admin)

												<?php
												$branchname = \App\Models\Branch::select('id', 'office_name')->where('id',$admin->office_id)->first();
												?>
												<option value=&quot;<?php echo $admin->id; ?>&quot;><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
												@endforeach
											</select>
										</div>
										<div class=&quot;clearfix&quot;></div>
								    </div>
							    </div><div id=&quot;popover-content&quot;>
							    <div class=&quot;box-header with-border&quot;>
								    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
										<label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Note</label>
										<div class=&quot;col-sm-9&quot;>
										    <textarea id=&quot;assignnote&quot; class=&quot;form-control summernote-simple f13&quot; placeholder=&quot;Enter an note....&quot; type=&quot;text&quot;></textarea>
										</div>
										<div class=&quot;clearfix&quot;></div>
								    </div>
							    </div>
								<div class=&quot;box-header with-border&quot;>
								    <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
										<label for=&quot;inputEmail3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Date</label>
										<div class=&quot;col-sm-9&quot;>
											<input type=&quot;date&quot; class=&quot;form-control f13&quot; placeholder=&quot;yyyy-mm-dd&quot; id=&quot;popoverdatetime&quot; value=&quot;<?php echo date('Y-m-d');?>&quot;name=&quot;popoverdate&quot;>
										</div>
										<div class=&quot;clearfix&quot;></div>
								    </div>
							    </div>

                                <div class=&quot;form-group row&quot; style=&quot;margin-bottom:12px&quot; >
                                    <label for=&quot;inputSub3&quot; class=&quot;col-sm-3 control-label c6 f13&quot; style=&quot;margin-top:8px&quot;>Group</label>
                                    <div class=&quot;col-sm-9&quot;>
                                        <select class=&quot;assigneeselect2 form-control selec_reg&quot; id=&quot;task_group&quot; name=&quot;task_group&quot;>
                                            <option value=&quot;&quot;>Select</option>
                                            <option value=&quot;Call&quot;>Call</option>
                                            <option value=&quot;Checklist&quot;>Checklist</option>
                                            <option value=&quot;Review&quot;>Review</option>
                                            <option value=&quot;Query&quot;>Query</option>
                                            <option value=&quot;Urgent&quot;>Urgent</option>
                                        </select>
                                    </div>
                                    <div class=&quot;clearfix&quot;></div>
                                </div>

								<input id=&quot;assign_client_id&quot;  type=&quot;hidden&quot; value=&quot;{{base64_encode(convert_uuencode(@$fetchedData->id))}}&quot;>
							    <div class=&quot;box-footer&quot; style=&quot;padding:10px 0&quot;>
							    <div class=&quot;row&quot;>
									<input type=&quot;hidden&quot; value=&quot;&quot; id=&quot;popoverrealdate&quot; name=&quot;popoverrealdate&quot; />
							    </div>
							    <div class=&quot;row text-center&quot;>
									<div class=&quot;col-md-12 text-center&quot;>
									<button  class=&quot;btn btn-danger&quot; id=&quot;assignUser&quot;>Assign User</button>
									</div>
							    </div>
					    </div>" data-original-title="" title="" style="background: rgba(255, 255, 255, 0.95); color: var(--primary-color); padding: 10px; font-size: 13px;width:100% !important;"> Action</button>
							<?php
									// PostgreSQL doesn't accept empty strings for integer columns - check before querying
									$agent = null;
									if(!empty(@$fetchedData->agent_id) && @$fetchedData->agent_id !== '') {
										$agent = \App\Models\Agent::select('id', 'full_name', 'email')->where('id', @$fetchedData->agent_id)->first();
									}
									if($agent){
										?>
										<div class="client_assign client_info_tags" style="margin-top: 12px;">
																<span class=""><b>Agent:</b></span>
																@if($agent)
																<div class="client_info">
																	<div class="cl_logo">{{substr(@$agent->full_name, 0, 1)}}</div>
																	<div class="cl_name">
																		<span class="name">{{@$agent->full_name}}</span>
																		<span class="email">{{@$agent->email}}</span>
																	</div>
																</div>
																@else
																	-
																@endif
															</div>
										<?php
									}
								?>
						</div>
						
						<!-- Personal Details Section -->
						<div class="card-header">
							<div style="display: flex; align-items: center; gap: 8px;">
								<h4>Personal Details</h4>
								<a href="javascript:;" datatype="not_picked_call" class="not_picked_call" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 3px 10px; border-radius: 6px; font-size: 10px; font-weight: 600; letter-spacing: 0.5px; text-decoration: none;">NP</a>
							</div>
							<a href="javascript:;" datatype="note" class="create_note_d"><i class="fa fa-plus"></i> Add Notes</a>
						</div>
						<div class="card-body">
							<p class="clearfix">
								<span class="float-start">Last Updated</span>
								<span class="float-end text-muted">
									<?php
									if( isset($fetchedData->updated_at) && $fetchedData->updated_at != "" ){
										echo date('d/m/Y', strtotime('-5 hours 30 minute', strtotime($fetchedData->updated_at)));
									} else {
										echo '—';
									} ?>
								</span>
							</p>
						    <p class="clearfix">
								<span class="float-start">Date Of Birth / Age</span>
								<span class="float-end text-muted">
									<?php
										if($fetchedData->dob != '' && $fetchedData->dob != '0000-00-00'){
										    echo $dob = date('d/m/Y', strtotime($fetchedData->dob));
										}
										?>	<?php
										if($fetchedData->age != ''){
										    echo ' / '.$fetchedData->age;
										}
									?>
								</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Visa:</span>
								<span class="float-end text-muted">{{$fetchedData->visa_type}}
									<?php
										if($fetchedData->visa_opt != ''){
										    //echo '<br>'.$fetchedData->visa_opt;
										    echo $fetchedData->visa_opt;
										}
									?>
								</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Visa Expiry:</span>
								<span class="float-end text-muted"><?php
										if($fetchedData->visaExpiry != '' && $fetchedData->visaExpiry != '0000-00-00'){
										    echo date('d/m/Y', strtotime($fetchedData->visaExpiry));
										}
										?>
								</span>
							</p>


							<p class="clearfix">
								<span class="float-start">Phone No:</span>
								<span class="float-end text-muted">
                                    {{--$fetchedData->phone--}} {{--@if($fetchedData->att_phone != '') / {{$fetchedData->att_phone}} @endif--}}
                                    <?php
                                    // For leads (type='lead'), use the phone from $fetchedData which comes from leads table
                                    // For clients, check ClientPhone table first, then admins table
                                    if( isset($fetchedData->type) && $fetchedData->type == 'lead' ) {
                                        // Lead: use phone data passed from controller (already from leads table)
                                        $clientContacts = collect([
                                            (object)[
                                                'client_phone' => $fetchedData->phone,
                                                'client_country_code' => $fetchedData->country_code ?? '',
                                                'contact_type' => $fetchedData->contact_type ?? 'Personal'
                                            ]
                                        ]);
                                        // Add alternate phone if exists
                                        if($fetchedData->att_phone) {
                                            $clientContacts->push((object)[
                                                'client_phone' => $fetchedData->att_phone,
                                                'client_country_code' => $fetchedData->att_country_code ?? '',
                                                'contact_type' => 'Alternate'
                                            ]);
                                        }
                                    } elseif( \App\Models\ClientPhone::where('client_id', $fetchedData->id)->exists()) {
                                        $clientContacts = \App\Models\ClientPhone::select('client_phone','client_country_code','contact_type')->where('client_id', $fetchedData->id)->where('contact_type', '!=', 'Not In Use')->get();
                                    } else {
                                        if( \App\Models\Admin::where('id', $fetchedData->id)->exists()){
                                            $clientContacts = \App\Models\Admin::select('phone as client_phone','country_code as client_country_code','contact_type')->where('id', $fetchedData->id)->get();
                                        } else {
                                            $clientContacts = array();
                                        }
                                    }
                                    if( !empty($clientContacts) && count($clientContacts)>0 ){
                                        $phonenoStr = "";
                                        foreach($clientContacts as $conKey=>$conVal){
                                            //Check phone is verified or not
											$check_verified_phoneno = $conVal->client_country_code."".$conVal->client_phone;
											$verifiedNumber = \App\Models\VerifiedNumber::where('phone_number',$check_verified_phoneno)->where('is_verified', true)->first();


                                            if( isset($conVal->client_country_code) && $conVal->client_country_code != "" ){
                                                $client_country_code = $conVal->client_country_code;
                                            } else {
                                                $client_country_code = "";
                                            }

                                            if( isset($conVal->contact_type) && $conVal->contact_type != "" ){
												if( $conVal->contact_type == "Personal" ){
													if ( $verifiedNumber) {
														$phonenoStr .= $client_country_code."".$conVal->client_phone.'('.$conVal->contact_type .') <i class="fas fa-check-circle verified-icon fa-lg"></i> <br/>';
													} else {
														$phonenoStr .= $client_country_code."".$conVal->client_phone.'('.$conVal->contact_type .') <i class="far fa-circle unverified-icon fa-lg"></i> <br/>';
													}
												} else {
													$phonenoStr .= $client_country_code."".$conVal->client_phone.'('.$conVal->contact_type .') <br/>';
												}
											} else {
												$phonenoStr .= $client_country_code."".$conVal->client_phone.' <br/>';
                                            }
                                        }
                                        echo $phonenoStr;
                                    } else {
                                        echo "N/A";
                                    }?>
                                </span>
							</p>


							<p class="clearfix">
								<span class="float-start">Email / Is verified:</span>
								<span class="float-end text-muted">
								    {{$fetchedData->email}}

                                    <?php
                                    if( isset($fetchedData->manual_email_phone_verified) && $fetchedData->manual_email_phone_verified == '1' )
                                    {
                                        //echo '<span style="color:green;">/Already Verified</span>';
                                        echo '<i class="fas fa-check-circle verified-icon fa-lg"></i>';
                                    } else {
                                        //echo '<span style="color:red;">/Not Now</span>';
                                        echo '<i class="far fa-circle unverified-icon fa-lg"></i>';
                                    }?>
                                </span>
							</p>

                            <?php if( isset($fetchedData->email_verified_at) && $fetchedData->email_verified_at != "" ){ ?>
                                <p class="clearfix">
                                    <span class="float-start">Email Verified At:</span>
                                    <span class="float-end text-muted">{{ date('d/m/Y',strtotime($fetchedData->email_verified_at))}}</span>
                                </p>
                            <?php } ?>

							<p class="clearfix">
								<span class="float-start">City:</span>
								<span class="float-end text-muted">{{$fetchedData->city}}</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Nominated Occupation:</span>
								<span class="float-end text-muted">{{$fetchedData->nomi_occupation}}</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Highest Qualification in Australia:</span>
								<span class="float-end text-muted">{{$fetchedData->high_quali_aus}}</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Highest Qualification Overseas:</span>
								<span class="float-end text-muted">{{$fetchedData->high_quali_overseas}}</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Work experience Australia:</span>
								<span class="float-end text-muted">{{$fetchedData->relevant_work_exp_aus}}</span>
							</p>
								<p class="clearfix">
								<span class="float-start">Work experience Offshore:</span>
								<span class="float-end text-muted">{{$fetchedData->relevant_work_exp_over}}</span>
							</p>
							<p class="clearfix">
								<span class="float-start">Overall English score: </span>
								<?php
									$testscores = \App\Models\TestScore::where('client_id', $fetchedData->id)->where('type', 'client')->first();
								?>
								<span class="float-end text-muted">{{ isset($fetchedData->married_partner) ? $fetchedData->married_partner : '' }}

                                  <?php /* if(@$testscores->score_2 != ''){ echo @$testscores->score_2; }else{ echo '-'; } ?> / <?php if(@$testscores->score_3 != ''){ echo @$testscores->score_3; }else{ echo '-'; } */ ?></span>
							</p>
							<p class="clearfix">
								<span class="float-start">Preferred Intake:</span>
								<span class="float-end text-muted"><?php if($fetchedData->preferredIntake != ''){ ?>{{date('M Y', strtotime($fetchedData->preferredIntake))}}<?php } ?></span>
							</p>
							 <p class="clearfix">
								<span class="float-start">Naati/PY</span>
								<span class="float-end text-muted"><?php if($fetchedData->naati_py != ''){ ?>{{$fetchedData->naati_py}}<?php } ?></span>
							</p>
							<div class="clearfix">
								<span class="float-start">Client Portal:</span>
								<div class="custom-switches float-end">
									<label class="custom-switch">
										<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked>
										<span class="custom-switch-indicator"></span>
									</label>
								</div>
							</div>
							<?php
								// PostgreSQL doesn't accept empty strings for integer columns - check before querying
								$addedby = null;
								if(!empty(@$fetchedData->user_id) && @$fetchedData->user_id !== '') {
									$addedby = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id', @$fetchedData->user_id)->first();
								}
							?>
							<div class="client_added client_info_tags">
								<span class="">Added By:</span>
								@if($addedby)
								<div class="client_info">
									<div class="cl_logo">{{substr(@$addedby->first_name, 0, 1)}}</div>
									<div class="cl_name">
										<span class="name">{{@$addedby->first_name}}</span>
										<span class="email">{{@$addedby->email}}</span>
									</div>
								</div>
								@else
									-
								@endif
							</div>
								<?php
                                use Illuminate\Support\Str;
                                //dd($fetchedData->assignee);
                                if( Str::contains($fetchedData->assignee, ',')){
                                    $assigneeUArr = explode(",",$fetchedData->assignee);
                                    $assigneeArr = \App\Models\Admin::select('id', 'first_name', 'last_name')->whereIn('id',$assigneeUArr)->get();
                                } else {
                                    $assigneeU = $fetchedData->assignee;
                                    $assigneeArr = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id',$assigneeU)->get();
                                }
                                //dd($assigneeArr);
                                ?>
							<div class="client_assign client_info_tags">
								<span class="">Assignee:</span>
								<span class="float-end text-muted">
								      <a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary openassigneeshow btn-sm"><i class="fa fa-plus"></i> Edit</a>
								    </span>
								    <div class="clearfix"></div>

								<?php
                                if( !empty($assigneeArr) && count($assigneeArr) >0 ){
                                    foreach ($assigneeArr as $assignee) {
                                ?>

								{{-- @if($assignee) --}}
                                <div class="client_info">
                                    <div class="cl_logo">{{substr(@$assignee->first_name, 0, 1)}}</div>
                                    <div class="cl_name">
                                        <span class="name">{{@$assignee->first_name}}</span>
                                        <!--<span class="email">{{--@$assignee->email--}}</span>-->
                                    </div>
                                </div>
                                <?php
                                    }
                                }
                                else { echo "-"; } ?>

								{{-- @else --}}
									<!-- -  -->
								{{-- @endif --}}

								<div class="assigneeshow" style="display:none;">
								    <table>
								        <tr>
								            <td><select class="form-control select2" id="changeassignee" name="changeassignee[]" multiple="multiple">
						                 	<?php
												foreach(\App\Models\Admin::select('id', 'office_id', 'first_name', 'last_name')->where('role','!=',7)->orderby('first_name','ASC')->get() as $admin){
													$branchname = \App\Models\Branch::select('id', 'office_name')->where('id',$admin->office_id)->first();
											?>
												<option value="<?php echo $admin->id; ?>"><?php echo $admin->first_name.' '.$admin->last_name.' ('.@$branchname->office_name.')'; ?></option>
											<?php } ?>
												</select></td>
											<td><a class="saveassignee btn btn-success" data-id="<?php echo $fetchedData->id; ?>" href="javascript:;">Save</a></td>
											<td><a class="closeassigneeshow" href="javascript:;"><i class="fa fa-times"></i></a></td>
								        </tr>
								    </table>
								</div>
							</div>
							<div class="client_assign client_info_tags">
								<p class="clearfix">
                                    <span class="float-start">Services Taken:</span>
                                    <!--<span class="float-end text-muted">
                                        <a href="javascript:;" data-id="{{--$fetchedData->id--}}" class="btn btn-primary serviceTaken btn-sm"><i class="fa fa-plus"></i> Add</a>
                                    </span>-->
                                </p>

                                 <div class="client_info">
								    <ul style="margin-left: -35px;">
										<?php
                                        $serviceTakenArr = \App\Models\clientServiceTaken::where('client_id', $fetchedData->id )->orderBy('created_at', 'desc')->get();
                                        //dd($serviceTakenArr);
                                        if( !empty($serviceTakenArr) && count($serviceTakenArr) >0 ){
                                            foreach ($serviceTakenArr as $tokenkey => $tokenval) {
                                                if($tokenval['service_type']  == "Migration") {
                                                    echo $tokenval['service_type']."-".htmlspecialchars($tokenval['mig_ref_no'])."-".htmlspecialchars($tokenval['mig_service'])."-".htmlspecialchars($tokenval['mig_notes']). "<br/>";
                                                } else if($tokenval['service_type']  == "Education") {
                                                    echo $tokenval['service_type']."-".htmlspecialchars($tokenval['edu_course'])."-".htmlspecialchars($tokenval['edu_college'])."-".htmlspecialchars($tokenval['edu_service_start_date'])."-".htmlspecialchars($tokenval['edu_notes']). "<br/>";
                                                }
                                            }
                                        } ?>
									</ul>
								</div>
                             </div>

                             <div class="">
								<p class="clearfix">
                                    <span class="float-start">Related files:</span>
                                </p>

								<div class="client_info">
								    <ul >
										<?php
											//$relatedclientss = \App\Models\Admin::select('id',  'first_name', 'last_name')->whereRaw("FIND_IN_SET($fetchedData->id,related_files)")->get();
											//foreach($relatedclientss AS $res){
										?>
											<!--<li><a target="_blank" href="{{--URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$res->id)))--}}">{{--$res->first_name--}} {{--$res->last_name--}}</a></li>-->
										<?php //} ?>

										<?php
										if($fetchedData->related_files != ''){
											$exploder = explode(',', $fetchedData->related_files);

										?>
										<?php   
										if(!empty($exploder)) {
											foreach($exploder AS $EXP){
												// PostgreSQL doesn't accept empty strings for integer columns - filter empty values
												if(!empty(trim($EXP)) && trim($EXP) !== '') {
													$relatedclients = \App\Models\Admin::where('id', trim($EXP))->first();
													if($relatedclients) {
										?>
														<li><a target="_blank" href="{{URL::to('/clients/detail/'.base64_encode(convert_uuencode(@$relatedclients->id)))}}">{{$relatedclients->first_name}} {{$relatedclients->last_name}}</a></li>
										<?php 
													}
												}
											} 
										}
										?>
										<?php } ?>
									</ul>
								</div>
							</div>

							<p class="clearfix">
								<span class="float-start">Tag(s):</span>
								<span class="float-end text-muted">
									<a href="javascript:;" data-id="{{$fetchedData->id}}" class="btn btn-primary opentagspopup btn-sm"><i class="fa fa-plus"></i> Add</a>
								</span>
							</p>
							<p>
								<?php
									$tagList = array();
									if(!empty($fetchedData->tagname)){
										$rawTags = explode(',', $fetchedData->tagname);
										foreach($rawTags as $rawTag){
											$cleanTag = trim($rawTag);
											if($cleanTag !== ''){
												$tagList[] = $cleanTag;
											}
										}
									}
								?>
								@if(!empty($tagList))
									@foreach($tagList as $tagLabel)
										<span class="ui label ag-flex ag-align-center ag-space-between" style="display: inline-flex;">
											<span class="col-hr-1" style="font-size: 12px;">{{ $tagLabel }}</span>
										</span>
									@endforeach
								@else
									<span class="text-muted">No tags added</span>
								@endif
							</p>
						</div>
					</div>
				</div> <!-- end personal-details-container -->

			<!--<div class="col-12 col-md-12 col-lg-12">-->
              
             
			<div class="right_section">
				<div class="card">
						<div class="card-body">
							@php
								$allowedTabs = [
									'activities',
									'noteterm',
									'application',
									'interested_service',
									'documents',
									'migrationdocuments',
									'alldocuments',
									'notuseddocuments',
									'accounts',
									'conversations'
								];
								$tabAliases = [
									'notestrm' => 'noteterm'
								];
								$allowedTabSlugs = array_unique(array_merge($allowedTabs, array_keys($tabAliases)));
								$requestedTab = Request::route('tab') ?? Request::get('tab');
								if (empty($requestedTab) || !in_array($requestedTab, $allowedTabSlugs, true)) {
									$requestedTab = 'activities';
								}
								$activeTab = $tabAliases[$requestedTab] ?? $requestedTab;
								$activeTabSlug = array_search($activeTab, $tabAliases, true);
								if ($activeTabSlug === false) {
									$activeTabSlug = $requestedTab;
								}
								$detailBaseUrl = Request::route() && Request::route()->getName() === 'leads.detail'
									? url('/leads/detail/'.$encodeId)
									: url('/clients/detail/'.$encodeId);
							@endphp
							<ul class="nav nav-pills" id="client_tabs" role="tablist" data-base-url="{{ $detailBaseUrl }}" data-active-tab="{{ $activeTabSlug }}">
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'activities' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="activities" id="activities-tab" href="#activities" role="tab" aria-controls="activities" aria-selected="{{ $activeTab === 'activities' ? 'true' : 'false' }}">Activities</a>
								</li>

								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'noteterm' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="notestrm" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="{{ $activeTab === 'noteterm' ? 'true' : 'false' }}">Notes & Terms</a>
								</li>

								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'application' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="application" id="application-tab" href="#application" role="tab" aria-controls="application" aria-selected="{{ $activeTab === 'application' ? 'true' : 'false' }}">Applications</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'interested_service' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="interested_service" id="interested_service-tab" href="#interested_service" role="tab" aria-controls="interested_service" aria-selected="{{ $activeTab === 'interested_service' ? 'true' : 'false' }}">Interested Services</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'documents' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="documents" id="documents-tab" href="#documents" role="tab" aria-controls="documents" aria-selected="{{ $activeTab === 'documents' ? 'true' : 'false' }}">Education Documents</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'migrationdocuments' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="migrationdocuments" id="migrationdocuments-tab" href="#migrationdocuments" role="tab" aria-controls="migrationdocuments" aria-selected="{{ $activeTab === 'migrationdocuments' ? 'true' : 'false' }}">Migration Documents</a>
								</li>

                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'alldocuments' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="alldocuments" id="alldocuments-tab" href="#alldocuments" role="tab" aria-controls="alldocuments" aria-selected="{{ $activeTab === 'alldocuments' ? 'true' : 'false' }}">Documents</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ $activeTab === 'notuseddocuments' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="notuseddocuments" id="notuseddocuments-tab" href="#notuseddocuments" role="tab" aria-controls="notuseddocuments" aria-selected="{{ $activeTab === 'notuseddocuments' ? 'true' : 'false' }}">Not Used Documents</a>
                                </li>

								<li class="nav-item">
									{{-- <a class="nav-link" data-bs-toggle="tab" id="appointments-tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">Appointments</a> --}}
								</li>

								<!--<li class="nav-item">
									<a class="nav-link" data-bs-toggle="tab" id="noteterm-tab" href="#noteterm" role="tab" aria-controls="noteterm" aria-selected="false">Notes & Terms</a>
								</li>-->

								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'accounts' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="accounts" id="accounts-tab" href="#accounts" role="tab" aria-controls="accounts" aria-selected="{{ $activeTab === 'accounts' ? 'true' : 'false' }}">Accounts</a>
								</li>
								<li class="nav-item">
									<a class="nav-link {{ $activeTab === 'conversations' ? 'active' : '' }}" data-bs-toggle="tab" data-tab="conversations" id="conversations-tab" href="#conversations" role="tab" aria-controls="conversations" aria-selected="{{ $activeTab === 'conversations' ? 'true' : 'false' }}">Conversations</a>
								</li>
								<!--<li class="nav-item">
									<a class="nav-link" data-bs-toggle="tab" id="other_info-tab" href="#other_info" role="tab" aria-controls="other_info" aria-selected="false">Other Information</a>
								</li>-->
							</ul>
							<div class="tab-content" id="clientContent" style="padding-top:15px;">
								<div class="tab-pane fade {{ $activeTab === 'activities' ? 'show active' : '' }}" id="activities" role="tabpanel" aria-labelledby="activities-tab">

								<!-- Activities Filter Bar - Permanently Visible -->
								<div class="activities-filter-bar">
									<form action="{{ $detailBaseUrl }}" method="get" id="activitiesFilterForm">
										@if($activeTabSlug && $activeTabSlug !== 'activities')
											<input type="hidden" name="tab" value="{{ $activeTabSlug }}">
										@endif
										
										<div class="row align-items-end">
											<!-- Search Box -->
											<div class="col-md-3 col-lg-2">
												<div class="form-group mb-0">
													<label for="activity_search" class="form-label small text-muted mb-1">Search</label>
													<input type="text" 
														   class="form-control form-control-sm" 
														   id="activity_search" 
														   name="keyword" 
														   value="{{ Request::get('keyword', '') }}" 
														   placeholder="Search activities...">
												</div>
											</div>

											<!-- Activity Type Filters -->
											<div class="col-md-6 col-lg-7">
												<div class="form-group mb-0">
													<label class="form-label small text-muted mb-1 d-block">Type</label>
													<div class="activity-type-filters" style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center;">
										<?php
														// Main filter buttons (always visible)
														$mainTypes = [
															'all' => 'All',
															'notes' => 'Notes'
														];
														
														// Dropdown types (all other types)
														$dropdownTypes = [
															'documents' => 'Documents',
															'action' => 'Action',
															'accounting' => 'Accounting',
															'messages' => 'Messages',
															'calls' => 'Calls',
															'reviews' => 'Reviews',
															'applications' => 'Applications',
															'services' => 'Services',
															'status' => 'Status',
															'checkins' => 'Check-ins',
															'other' => 'Other'
														];
														
														$selectedType = Request::get('activity_type', 'all');
														
														// Display main buttons
														foreach($mainTypes as $key => $label) {
															$active = ($selectedType == $key) ? 'active' : '';
															echo '<button type="button" class="btn btn-sm activity-type-btn ' . $active . '" data-type="' . $key . '" style="border: 1px solid #ddd; background: #fff; padding: 5px 12px; font-size: 12px; border-radius: 4px; white-space: nowrap;">' . $label . '</button>';
														}
														
														// Display dropdown for additional types
														$dropdownActive = in_array($selectedType, array_keys($dropdownTypes));
														$dropdownLabel = $dropdownActive ? $dropdownTypes[$selectedType] : 'More...';
														?>
														<div class="dropdown d-inline-block">
															<button type="button" 
																	class="btn btn-sm activity-type-btn dropdown-toggle <?php echo $dropdownActive ? 'active' : ''; ?>" 
																	data-bs-toggle="dropdown" 
																	aria-expanded="false"
																	style="border: 1px solid #ddd; background: <?php echo $dropdownActive ? '#6777ef' : '#fff'; ?>; color: <?php echo $dropdownActive ? '#fff' : '#000'; ?>; padding: 5px 12px; font-size: 12px; border-radius: 4px; white-space: nowrap;">
																<?php echo $dropdownLabel; ?>
															</button>
															<ul class="dropdown-menu" style="font-size: 12px;">
																<?php foreach($dropdownTypes as $key => $label): ?>
																	<li>
																		<a class="dropdown-item activity-type-dropdown-item <?php echo ($selectedType == $key) ? 'active' : ''; ?>" 
																		   href="javascript:;" 
																		   data-type="<?php echo $key; ?>"
																		   style="<?php echo ($selectedType == $key) ? 'background-color: #6777ef; color: #fff;' : ''; ?>">
																			<?php echo $label; ?>
																		</a>
																	</li>
																<?php endforeach; ?>
															</ul>
														</div>
														<input type="hidden" name="activity_type" id="activity_type_input" value="{{ $selectedType }}">
													</div>
												</div>
											</div>

											<!-- Date Range Filter -->
											<div class="col-md-3 col-lg-3">
												<div class="form-group mb-0">
													<label for="date_from" class="form-label small text-muted mb-1">Date Range</label>
													<div class="d-flex gap-2">
														<input type="text" 
															   class="form-control form-control-sm date-filter" 
															   id="date_from" 
															   name="date_from" 
															   value="{{ Request::get('date_from', '') }}" 
															   placeholder="From" 
															   autocomplete="off"
															   style="flex: 1;">
														<input type="text" 
															   class="form-control form-control-sm date-filter" 
															   id="date_to" 
															   name="date_to" 
															   value="{{ Request::get('date_to', '') }}" 
															   placeholder="To" 
															   autocomplete="off"
															   style="flex: 1;">
													</div>
												</div>
											</div>
										</div>

										<!-- Action Buttons -->
										<div class="row mt-2">
											<div class="col-12">
												<button type="submit" class="btn btn-primary btn-sm" style="margin-right: 8px;">
													<i class="fas fa-search"></i> Apply Filters
												</button>
												<a href="{{ $detailBaseUrl }}" class="btn btn-secondary btn-sm">
													<i class="fas fa-redo"></i> Reset
												</a>
												<?php if(Request::get('keyword') || Request::get('activity_type') != 'all' || Request::get('date_from') || Request::get('date_to')): ?>
													<span class="badge bg-info ms-2" style="vertical-align: middle; padding: 6px 10px;">
														Filters Active
													</span>
												<?php endif; ?>
											</div>
										</div>
									</form>
								</div>

								<div class="activities">
										<?php
										// Build query with filters
										$query = \App\Models\ActivitiesLog::where('activities_logs.client_id', $fetchedData->id);
										
										// Keyword search filter
										$keyword_search = Request::get('keyword', '');
										if($keyword_search != "") {
											$query->where(function($q) use ($keyword_search) {
												$q->where('activities_logs.description', 'like', '%'.$keyword_search.'%')
												  ->orWhere('activities_logs.subject', 'like', '%'.$keyword_search.'%');
											});
										}
										
										// Activity type filter
										$activity_type = Request::get('activity_type', 'all');
										if($activity_type != 'all') {
											switch($activity_type) {
												case 'notes':
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%added a note%')
														  ->orWhere('activities_logs.subject', 'like', '%updated a note%')
														  ->orWhere('activities_logs.subject', 'like', '%deleted a note%');
													});
													break;
												case 'messages':
													$query->where('activities_logs.subject', 'like', '%sent a message%');
													break;
												case 'calls':
													$query->where(function($q) {
														$q->where('activities_logs.description', 'like', '%Call not picked%')
														  ->orWhere('activities_logs.subject', 'like', '%call%');
													});
													break;
												case 'reviews':
													$query->where('activities_logs.subject', 'like', '%review%');
													break;
												case 'documents':
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%document%')
														  ->orWhere('activities_logs.subject', 'like', '%uploaded%')
														  ->orWhere('activities_logs.subject', 'like', '%verified%');
													});
													break;
												case 'action':
													// Renamed from 'tasks' - Groups: Tasks, Actions
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%action%')
														  ->orWhere('activities_logs.subject', 'like', '%task%')
														  ->orWhere('activities_logs.subject', 'like', '%Completed action%')
														  ->orWhere('activities_logs.task_status', '=', 1);
													});
													break;
												case 'accounting':
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%receipt%')
														  ->orWhere('activities_logs.subject', 'like', '%invoice%')
														  ->orWhere('activities_logs.subject', 'like', '%payment%');
													});
													break;
												case 'applications':
													$query->where('activities_logs.subject', 'like', '%started an application%');
													break;
												case 'services':
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%interested service%')
														  ->orWhere('activities_logs.subject', 'like', '%service%');
													});
													break;
												case 'status':
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%status%')
														  ->orWhere('activities_logs.subject', 'like', '%rated%')
														  ->orWhere('activities_logs.subject', 'like', '%rating%');
													});
													break;
												case 'checkins':
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'like', '%check-in%')
														  ->orWhere('activities_logs.subject', 'like', '%session%')
														  ->orWhere('activities_logs.subject', 'like', '%commented%');
													});
													break;
												case 'other':
													// Exclude all known types
													$query->where(function($q) {
														$q->where('activities_logs.subject', 'not like', '%note%')
														  ->where('activities_logs.subject', 'not like', '%document%')
														  ->where('activities_logs.subject', 'not like', '%action%')
														  ->where('activities_logs.subject', 'not like', '%task%')
														  ->where('activities_logs.subject', 'not like', '%receipt%')
														  ->where('activities_logs.subject', 'not like', '%application%')
														  ->where('activities_logs.subject', 'not like', '%message%')
														  ->where('activities_logs.subject', 'not like', '%call%')
														  ->where('activities_logs.subject', 'not like', '%service%')
														  ->where('activities_logs.subject', 'not like', '%status%')
														  ->where('activities_logs.subject', 'not like', '%check-in%')
														  ->where('activities_logs.subject', 'not like', '%session%')
														  ->where('activities_logs.subject', 'not like', '%review%');
													});
													break;
											}
										}
										
										// Date range filter
										$date_from = Request::get('date_from', '');
										$date_to = Request::get('date_to', '');
										if($date_from != "") {
											$query->whereDate('activities_logs.created_at', '>=', date('Y-m-d', strtotime($date_from)));
										}
										if($date_to != "") {
											$query->whereDate('activities_logs.created_at', '<=', date('Y-m-d', strtotime($date_to)));
										}
										
										// Execute query
										$activities = $query->orderby('activities_logs.created_at', 'DESC')->get();

										//dd($activities);
                                        foreach($activities as $activit){
											$admin = \App\Models\Admin::select('id', 'first_name', 'last_name')->where('id', $activit->created_by)->first();
                                            /*if($activit->use_for != ""){
                                                $receiver = \App\Models\Admin::where('id', $activit->use_for)->first();
                                                if($receiver->first_name){
                                                    $reciver_name = "to <b>{$receiver->first_name}</b>";
                                                } else {
                                                    $reciver_name = "";
                                                }
                                            } else {
                                                $reciver_name = "";
                                            }*/

											?>
											<div class="activity" id="activity_{{$activit->id}}">
												<div class="activity-icon bg-primary text-white">
													<span>{{substr($admin->first_name, 0, 1)}}</span>
												</div>
												<div class="activity-detail">
												    <div class="activity-head">
												    	<div class="activity-title">
															<p><b>{{$admin->first_name}}</b>  <?php echo @$activit->subject; ?></p>
                                                    	</div>

                                                     	<div class="activity-date">
                                                          <span class="text-job">{{date('d M Y, H:i A', strtotime($activit->created_at))}}</span>
                                                        </div>
                                                    </div>

                                                    <div class="right" style="float: right;margin-top: -40px;">
                                                        <?php if($activit->pin == 1){?>
                                                            <div class="pined_note"><i class="fa fa-thumbtack" style="font-size: 12px;color: #6777ef;"></i></div>
                                                        <?php } ?>

                                                        <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                            <a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                            <div class="dropdown-menu">
                                                                @if(Auth::user()->role == 1)
                                                                <a data-id="{{$activit->id}}" data-href="deleteactivitylog" class="dropdown-item deleteactivitylog" href="javascript:;" >Delete</a>
                                                               @endif
                                                                <?php if($activit->pin == 1){ ?>
                                                                    <a data-id="<?php echo $activit->id;?>"  class="dropdown-item pinactivitylog" href="javascript:;" >UnPin</a>
                                                                <?php
                                                                } else { ?>
                                                                    <a data-id="<?php echo $activit->id;?>"  class="dropdown-item pinactivitylog" href="javascript:;" >Pin</a>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>

													@if(!empty($activit->description))
                                                        @php
                                                            $description = $activit->description;
                                                        @endphp

                                                        @if(strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false)
                                                            <p>{!! htmlentities($description) !!}</p>
                                                        @else
                                                            <p>{!! $description !!}</p>
                                                        @endif
                                                    @endif

                                                    @if(isset($activit->task_status) && $activit->task_status == '1')
														<p style="color:#4caf50;"><b>Completed</b></p>
													@endif

                                                    @if($activit->followup_date != '')
														<p>{!!$activit->followup_date!!}</p>
													@endif

                                                    @if($activit->task_group != '')
														<p>{!!$activit->task_group!!}</p>
													@endif
												</div>
											</div>
											<?php
												}
											?>
									</div>
								</div>
								<div class="tab-pane fade {{ $activeTab === 'application' ? 'show active' : '' }}" id="application" role="tabpanel" aria-labelledby="application-tab">
									<div class="card-header-action text-end if_applicationdetail" style="padding-bottom:15px;">
										<a href="javascript:;" data-bs-toggle="modal" data-bs-target=".add_appliation" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="table-responsive if_applicationdetail">
										<table class="table text_wrap table-2">
											<thead>
												<tr>
													<th>Name</th>
													<th>Workflow</th>
													<th>Current Stage</th>
													<th>Status</th>
													<th>Start Date</th>
													<th>End Date</th>

													<th></th>
												</tr>
											</thead>
											<tbody class="applicationtdata">
											<?php
											$application_data=\App\Models\Application::where('client_id', $fetchedData->id)->orderby('created_at','Desc')->get();
											if(count($application_data) > 0){
											foreach($application_data as $alist){
												$productdetail = \App\Models\Product::where('id', $alist->product_id)->first();
												$partnerdetail = \App\Models\Partner::where('id', $alist->partner_id)->first();
												$PartnerBranch = \App\Models\PartnerBranch::where('id', $alist->branch)->first();
												$workflow = \App\Models\Workflow::where('id', $alist->workflow)->first();

                                                $application_assign_count = \App\Models\Note::where('type','client')->whereNotNull('client_id')->where('folloup',1)->where('status',0)->where('application_id',$alist->id)->where('client_id',$fetchedData->id)->count();
                                                //dd($application_assign_count);
												?>
												<tr id="id_{{$alist->id}}">
													<td>
                                                      <a class="openapplicationdetail" data-id="{{$alist->id}}" href="javascript:;" style="display:block;">
                                                        {{@$productdetail->name}}

                                                        <?php if( $application_assign_count > 0 ) { ?>
                                                           <span class="countTotalActivityAction" style="background: #1f1655;padding: 0px 5px;border-radius: 50%;color: #fff;margin-left: 5px;">{{ $application_assign_count }}</span>
                                                        <?php } ?>
                                                      </a>
                                                      <small>{{@$partnerdetail->partner_name}} ({{@$PartnerBranch->name}})</small>
                                                    </td>
													<td>{{@$workflow->name}}</td>
													<td>{{@$alist->stage}}</td>
													<td>
                                                      @if(@$alist->status == 0)
                                                      <span class="ag-label--circular" style="color: #6777ef" >In Progress</span>
                                                      @elseif(@$alist->status == 1)
                                                      <span class="ag-label--circular" style="color: #6777ef" >Completed</span>
                                                      @elseif(@$alist->status == 2)
                                                      <span class="ag-label--circular" style="color: red;" >Discontinued</span>
                                                      @elseif(@$alist->status == 3)
                                                      <span class="ag-label--circular" style="color: red;" >Cancelled</span>
                                                      @elseif(@$alist->status == 4)
                                                      <span class="ag-label--circular" style="color: red;" >Withdrawn</span>
                                                      @elseif(@$alist->status == 5)
                                                      <span class="ag-label--circular" style="color: red;" >Deferred</span>
                                                      @elseif(@$alist->status == 6)
                                                      <span class="ag-label--circular" style="color: red;" >Future</span>
                                                      @elseif(@$alist->status == 7)
                                                      <span class="ag-label--circular" style="color: red;" >VOE</span>
                                                      @elseif(@$alist->status == 8)
                                                      <span class="ag-label--circular" style="color: red;" >Refund</span>
                                                      @endif
                                                    </td>

													<td><?php if(@$alist->start_date != ''){ echo date('d/m/Y', strtotime($alist->start_date)); } ?></td>
													<td><?php if(@$alist->end_date != ''){ echo date('d/m/Y', strtotime($alist->end_date)); } ?></td>

                                                  <?php
                                                  if( Auth::user()->role == 1 )
                                                  { //super admin or admin
                                                  ?>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">

																<a class="dropdown-item has-icon" href="javascript:;" onClick="deleteAction({{@$alist->id}}, 'applications')"><i class="fas fa-trash"></i> Delete</a>
															</div>
														</div>
													</td>

                                                   <?php
                                                    }?>
												</tr>
												<?php
											}

											?>

											</tbody>
											<?php
											}else{ ?>
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="10">
														No Record found
													</td>
												</tr>
											</tbody>
									<?php	} ?>
										</table>
									</div>
									<div class="ifapplicationdetailnot" style="display:none;">
										<h4>Please wait ...</h4>
									</div>
								</div>
                                      
								<div class="tab-pane fade {{ $activeTab === 'interested_service' ? 'show active' : '' }}" id="interested_service" role="tabpanel" aria-labelledby="interested_service-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<a href="javascript:;" data-bs-toggle="modal" data-bs-target=".add_interested_service" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
									</div>
									<div class="interest_serv_list">

									<?php
									$inteservices = \App\Models\InterestedService::where('client_id',$fetchedData->id)->orderby('created_at', 'DESC')->get();
									foreach($inteservices as $inteservice){
										$workflowdetail = \App\Models\Workflow::where('id', $inteservice->workflow)->first();
										 $productdetail = \App\Models\Product::where('id', $inteservice->product)->first();
										$partnerdetail = \App\Models\Partner::where('id', $inteservice->partner)->first();
										$PartnerBranch = \App\Models\PartnerBranch::where('id', $inteservice->branch)->first();
										$admin = \App\Models\Admin::select('id','first_name', 'last_name')->where('id', $inteservice->user_id)->first();
									?>
										<div class="interest_column">
											<?php
												if($inteservice->status == 1){
													?>
													<div class="interest_serv_status status_active">
														<span>Converted</span>
													</div>
													<?php
												}else{
													?>
													<div class="interest_serv_status status_default">
														<span>Draft</span>
													</div>
													<?php
												}
												?>
											<?php
												$client_revenue = '0.00';
												if($inteservice->client_revenue != ''){
													$client_revenue = $inteservice->client_revenue;
												}
												$partner_revenue = '0.00';
												if($inteservice->partner_revenue != ''){
													$partner_revenue = $inteservice->partner_revenue;
												}
												$discounts = '0.00';
												if($inteservice->discounts != ''){
													$discounts = $inteservice->discounts;
												}
												$nettotal = $client_revenue + $partner_revenue - $discounts;


												$totl = 0.00;
												$net = 0.00;
												$discount = 0.00;
												?>
											<div class="interest_serv_info">
												<h4>{{@$workflowdetail->name}}</h4>
												<h6>{{@$productdetail->name}}</h6>
												<p>{{@$partnerdetail->partner_name}}</p>
												<p>{{@$PartnerBranch->name}}</p>
											</div>
											<div class="interest_serv_fees">
												<div class="fees_col cus_col">
													<span class="cus_label">Product Fees</span>
													<span class="cus_value">AUD: <?php echo number_format($net,2,'.',''); ?></span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Sales Forecast</span>
													<span class="cus_value">AUD: <?php echo number_format($nettotal,2,'.',''); ?></span>
												</div>
											</div>
											<div class="interest_serv_date">
												<div class="date_col cus_col">
													<span class="cus_label">Expected Start Date</span>
													<span class="cus_value">{{$inteservice->start_date}}</span>
												</div>
												<div class="fees_col cus_col">
													<span class="cus_label">Expected Win Date</span>
													<span class="cus_value">{{$inteservice->exp_date}}</span>
												</div>
											</div>
											<div class="interest_serv_row">
												<div class="serv_user_data">
													<div class="serv_user_img"><?php echo substr($admin->first_name, 0, 1); ?></div>
													<div class="serv_user_info">
														<span class="serv_name">{{$admin->first_name}}</span>
														<span class="serv_create">{{date('Y-m-d', strtotime($inteservice->exp_date))}}</span>
													</div>
												</div>
												<div class="serv_user_action">
													<a href="javascript:;" data-id="{{$inteservice->id}}" class="btn btn-primary interest_service_view">View</a>
													<div class="dropdown d-inline dropdown_ellipsis_icon" style="margin-left:10px;">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
														<?php if($inteservice->status == 0){ ?>
															<a class="dropdown-item converttoapplication" data-id="{{$inteservice->id}}" href="javascript:;">Create Appliation</a>
														<?php } ?>
															<a data-id="{{$inteservice->id}}" data-href="deleteservices" class="dropdown-item deletenote" href="javascript:;">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>

									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade {{ $activeTab === 'documents' ? 'show active' : '' }}" id="documents" role="tabpanel" aria-labelledby="documents-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<!-- Upload disabled for Education Documents tab -->
										{{-- <div class="upload_document" style="display:inline-block;">
										<form method="POST" enctype="multipart/form-data" id="upload_form">
											@csrf
											<input type="hidden" name="clientid" value="{{$fetchedData->id}}">
											<input type="hidden" name="type" value="client">
												<input type="hidden" name="doctype" value="education">
											<input class="docupload" multiple type="file" name="document_upload[]"/>
											</form>
										</div> --}}
									</div>
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>File Name</th>
														<th>Added Date</th>
													</tr>
												</thead>
												<tbody class="tdata documnetlist">
										<?php
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('doc_type', 'education')->where('type','client')->orderby('created_at', 'DESC')->get();
										foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
										$addedByInfo = $admin->first_name . ' on ' . date('d/m/Y', strtotime($fetch->created_at));
										?>
													<tr class="drow document-row" id="id_{{$fetch->id}}" 
														data-doc-id="{{$fetch->id}}"
														data-file-name="<?php echo htmlspecialchars($fetch->file_name, ENT_QUOTES, 'UTF-8'); ?>"
														data-file-type="<?php echo htmlspecialchars($fetch->filetype, ENT_QUOTES, 'UTF-8'); ?>"
														data-myfile="<?php echo htmlspecialchars($fetch->myfile, ENT_QUOTES, 'UTF-8'); ?>"
														data-doc-type="education"
														data-is-education="true"
														title="Added by: <?php echo htmlspecialchars($addedByInfo, ENT_QUOTES, 'UTF-8'); ?>"
														style="cursor: context-menu;">
													<td  style="white-space: initial;">
														<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
															<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset('img/documents/'.$fetch->myfile); ?>','preview-container-documentlist')">
                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                            </a>
														</div>
													</td>
													<td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
												</tr>
												<?php } ?>
												</tbody>

											</table>
										</div>
									</div>
									<div class="grid_data griddata">
									<?php
									foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $fetch->user_id)->first();
									?>
										<div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
											<div class="grid_col">
												<div class="grid_icon">
													<i class="fas fa-file-image"></i>
												</div>
												<div class="grid_content">
													<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">

																<a target="_blank" class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
										<div class="clearfix"></div>
									</div>
                                     <!-- Container for File Preview -->
                            		<div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-documentlist">
                            			<p style="color:#000;">Click on a file to preview it here.</p>
                            		</div>
								</div>
                                      
								<div class="tab-pane fade {{ $activeTab === 'migrationdocuments' ? 'show active' : '' }}" id="migrationdocuments" role="tabpanel" aria-labelledby="migrationdocuments-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
										<!-- Upload disabled for Migration Documents tab -->
										{{-- <div class="migration_upload_document" style="display:inline-block;">
                                              <form method="POST" enctype="multipart/form-data" id="mig_upload_form">
                                                  @csrf
                                                  <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                                                  <input type="hidden" name="type" value="client">
                                                  <input type="hidden" name="doctype" value="migration">
                                                  <input class="migdocupload" multiple type="file" name="document_upload[]"/>
											</form>
										</div> --}}
									</div>
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
														<th>File Name</th>
														<th>Added By</th>

														<th>Added Date</th>
														<th></th>
													</tr>
												</thead>
												<tbody class="tdata migdocumnetlist">
										<?php
										$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('doc_type', 'migration')->where('type','client')->orderby('created_at', 'DESC')->get();
										//dd($fetchd);
										foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $fetch->user_id)->first();
										?>
													<tr class="drow" id="id_{{$fetch->id}}">
													<td  style="white-space: initial;">
														<div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
															<a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset('img/documents/'.$fetch->myfile); ?>','preview-container-migrationdocumentlist')">
                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                            </a>
														</div>
													</td>
													<td style="white-space: initial;"><?php echo $admin->first_name; ?></td>

													<td style="white-space: initial;"><?php echo date('d/m/Y', strtotime($fetch->created_at)); ?></td>
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item renamedoc" href="javascript:;">Rename</a>
																<a target="_blank" class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<?php
																$explodeimg = explode('.',$fetch->myfile);
																if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
																?>
																	<a target="_blank" class="dropdown-item" href="{{URL::to('/document/download/pdf')}}/<?php echo $fetch->id; ?>">PDF</a>
																	<?php } ?>
																<a download class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
															</div>
														</div>
													</td>
												</tr>
												<?php } ?>
												</tbody>

											</table>
										</div>
									</div>
									<div class="grid_data miggriddata">
									<?php
									foreach($fetchd as $fetch){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $fetch->user_id)->first();
									?>
										<div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
											<div class="grid_col">
												<div class="grid_icon">
													<i class="fas fa-file-image"></i>
												</div>
												<div class="grid_content">
													<span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">

																<a target="_blank" class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Preview</a>
																<a download class="dropdown-item" href="{{asset('img/documents')}}/<?php echo $fetch->myfile; ?>">Download</a>
																<a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletedocs" href="javascript:;">Delete</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
										<div class="clearfix"></div>
									</div>
                                  
                                     <!-- Container for File Preview -->
                                    <div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-migrationdocumentlist">
                                        <p style="color:#000;">Click on a file to preview it here.</p>
                                    </div>
								</div>



                                 <div class="tab-pane fade {{ $activeTab === 'alldocuments' ? 'show active' : '' }}" id="alldocuments" role="tabpanel" aria-labelledby="alldocuments-tab">
                                    <div class="card-header-action text-end" style="padding-bottom:15px;">
                                        <div class="document_layout_type">
                                            <a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
                                            <a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
                                        </div>
                                        <a href="javascript:;" class="btn btn-primary add_alldocument_doc"><i class="fa fa-plus"></i> Add Checklist</a>
                                        <button type="button" class="btn btn-info bulk-upload-toggle-btn ms-2"><i class="fas fa-upload"></i> Bulk Upload</button>
                                    </div>
                                    
                                    <!-- Bulk Upload Dropzone (Hidden by default) -->
                                    <div class="bulk-upload-dropzone-container" id="bulk-upload-documents" style="display: none; margin-bottom: 20px; padding: 0 15px;">
                                        <div class="bulk-upload-dropzone" 
                                             style="border: 2px dashed #4a90e2; border-radius: 8px; padding: 40px; 
                                                    text-align: center; background-color: #f8f9fa; cursor: pointer;">
                                            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #4a90e2; margin-bottom: 15px;"></i>
                                            <h4 style="color: #333; margin-bottom: 10px;">Drop files here or click to browse</h4>
                                            <p style="color: #666; margin-bottom: 0;">Supported: PDF, JPG, PNG, DOC, DOCX (Max 50MB per file)</p>
                                            <input type="file" class="bulk-upload-file-input" multiple style="display: none;" 
                                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        </div>
                                        <div class="bulk-upload-file-list" style="display: none; margin-top: 15px; padding: 15px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
                                            <strong style="color: #333;">Selected Files: <span class="file-count">0</span></strong>
                                            <div class="bulk-upload-files-container"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
                                        <div class="">
                                            <table class="table text_wrap">
                                                <thead>
                                                    <tr>
                                                        <th>Checklist</th>
                                                        <th>File Name</th>
                                                        <!--<th>Verified By</th>-->
                                                    </tr>
                                                </thead>
                                                <tbody class="tdata alldocumnetlist">
                                                    <?php
                                                    $fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->whereNull('not_used_doc')->where('doc_type', 'documents')->where('type','client')->orderby('updated_at', 'DESC')->get();
                                                    foreach($fetchd as $docKey=>$fetch)
                                                    {
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        $addedByInfo = $admin->first_name . ' on ' . date('d/m/Y', strtotime($fetch->created_at));
                                                        //Checklist verified by
                                                        /*if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                                                            $checklist_verified_Info = \App\Models\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                                                            $checklist_verified_by = $checklist_verified_Info->first_name;
                                                        } else {
                                                            $checklist_verified_by = 'N/A';
                                                        }

                                                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                                                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                                                        } else {
                                                            $checklist_verified_at = 'N/A';
                                                        }*/
                                                        ?>
                                                        <tr class="drow document-row" id="id_{{$fetch->id}}" 
                                                            data-doc-id="<?php echo $fetch->id;?>"
                                                            data-checklist-name="<?php echo htmlspecialchars($fetch->checklist, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-file-name="<?php echo htmlspecialchars($fetch->file_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-file-type="<?php echo htmlspecialchars($fetch->filetype, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-myfile="<?php echo htmlspecialchars($fetch->myfile, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-myfile-key="<?php echo isset($fetch->myfile_key) ? htmlspecialchars($fetch->myfile_key, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                                            data-doc-type="<?php echo htmlspecialchars($fetch->doc_type, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-user-role="<?php echo Auth::user()->role; ?>"
                                                            title="Added by: <?php echo htmlspecialchars($addedByInfo, ENT_QUOTES, 'UTF-8'); ?>"
                                                            style="cursor: context-menu;">
                                                            <td style="white-space: initial;">
                                                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                                                    <span><?php echo $fetch->checklist; ?></span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php
                                                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                                                    <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                                                        <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>" style="white-space: initial;">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-alldocumentlist')">
                                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                            </a>
                                                                        <?php } else {  //For old file upload
                                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                            $myawsfile = $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
                                                                            
                                                                            ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>" style="white-space: initial;">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-alldocumentlist')">
                                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                            </a>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php
                                                                }
                                                                else
                                                                {?>
                                                                    <div class="allupload_document" style="display:inline-block;">
                                                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                                                            @csrf
                                                                            <input type="hidden" name="clientid" value="{{$fetchedData->id}}">
                                                                            <input type="hidden" name="fileid" value="{{$fetch->id}}">
                                                                            <input type="hidden" name="type" value="client">
                                                                            <input type="hidden" name="doctype" value="documents">
                                                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                                                            <input class="alldocupload" data-fileid="<?php echo $fetch->id;?>" type="file" name="document_upload"/>
                                                                        </form>
                                                                    </div>
                                                                <?php
                                                                }?>
                                                            </td>
                                                            <!--<td id="docverifiedby_<?php //echo $fetch->id;?>">
                                                                <?php
                                                                //echo $checklist_verified_by. "<br>";
                                                                //echo $checklist_verified_at;
                                                                ?>
                                                            </td>-->
                                                        </tr>
                                                    <?php
                                                    } //end foreach?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="grid_data allgriddata">
                                        <?php
                                        foreach($fetchd as $fetch)
                                        {
                                            $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                            ?>
                                            <div class="grid_list" id="gid_<?php echo $fetch->id; ?>">
                                                <div class="grid_col">
                                                    <div class="grid_icon">
                                                        <i class="fas fa-file-image"></i>
                                                    </div>
                                                    <?php
                                                    if( isset($fetch->myfile) && $fetch->myfile != "")
                                                    { ?>
                                                        <div class="grid_content">
                                                            <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                                            <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                <a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu">
                                                                    <?php $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';?>

                                                                    <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                                        <a download class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Download</a>
                                                                    <?php } else {  //For old file upload?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                                        <a download class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Download</a>
                                                                    <?php } ?>

                                                                    <?php if( Auth::user()->role == 1 ){ //super admin ?>
                                                                    <a data-id="{{$fetch->id}}" class="dropdown-item deletenote" data-href="deletealldocs" href="javascript:;">Delete</a>
                                                                    <?php } ?>
                                                                    <a data-id="{{$fetch->id}}" class="dropdown-item verifydoc" data-doctype="documents" data-href="verifydoc" href="javascript:;">Verify</a>
                                                                    <a data-id="{{$fetch->id}}" class="dropdown-item notuseddoc" data-doctype="documents" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    }?>
                                                </div>
                                            </div>
                                        <?php
                                        } //end foreach ?>
                                        <div class="clearfix"></div>
                                    </div>
                                   
                                    <!-- Container for File Preview -->
                                    <div style="margin-left: 10px;" class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-alldocumentlist">
                                        <p style="color:#000;">Click on a file to preview it here.</p>
                                    </div>
                                    
                                    <!-- Bulk Upload Mapping Modal -->
                                    <div id="bulk-upload-mapping-modal" class="bulk-upload-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
                                        <div class="bulk-upload-modal-content">
                                            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                                <h3 style="margin: 0; color: #333;">Map Files to Checklists</h3>
                                                <button type="button" class="close-mapping-modal" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                                            </div>
                                            <div style="padding: 20px; overflow-x: auto;">
                                                <div id="bulk-upload-mapping-table"></div>
                                            </div>
                                            <div style="padding: 20px; border-top: 1px solid #e2e8f0;">
                                                <div class="bulk-upload-progress" id="bulk-upload-progress" style="display: none; margin-bottom: 15px;">
                                                    <div style="background: #e2e8f0; border-radius: 4px; overflow: hidden; height: 30px;">
                                                        <div class="progress-bar" id="bulk-upload-progress-bar" 
                                                             style="background: #4a90e2; height: 100%; color: white; display: flex; 
                                                                    align-items: center; justify-content: center; font-weight: bold; 
                                                                    transition: width 0.3s; width: 0%;">0%</div>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <button type="button" class="btn btn-secondary" id="cancel-bulk-upload">Cancel</button>
                                                    <button type="button" class="btn btn-primary" id="confirm-bulk-upload" style="margin-left: 10px;">Upload All</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $activeTab === 'notuseddocuments' ? 'show active' : '' }}" id="notuseddocuments" role="tabpanel" aria-labelledby="notuseddocuments-tab">
									<!--<div class="card-header-action text-end" style="padding-bottom:15px;">
										<div class="document_layout_type">
											<a href="javascript:;" class="list active"><i class="fas fa-list"></i></a>
											<a href="javascript:;" class="grid"><i class="fas fa-columns"></i></a>
										</div>
                                    </div>-->
									<div class="list_data col-6 col-md-6 col-lg-6" style="display:inline-block;vertical-align: top;">
										<div class="">
											<table class="table text_wrap">
												<thead>
													<tr>
                                                        <!--<th>SNo.</th>-->
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <!--<th>Verified By</th>-->
                                                        <th></th>
													</tr>
												</thead>
												<tbody class="tdata notuseddocumnetlist">
                                                    <?php
                                                    //$fetchd = \App\Models\Document::where('client_id',$fetchedData->id)->where('not_used_doc', 1)->where('doc_type', 'personal')->where('type','client')->orderby('updated_at', 'DESC')->get();
                                                    $fetchd = \App\Models\Document::where('client_id', $fetchedData->id)
                                                    ->where('not_used_doc', 1)
                                                    ->where('type','client')
                                                    ->where('doc_type','documents')
                                                    ->orderBy('type', 'DESC')->get();
                                                    //dd($fetchd);
                                                    foreach($fetchd as $notuseKey=>$fetch)
                                                    {
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        //Checklist verified by
                                                        /*if( isset($fetch->checklist_verified_by) && $fetch->checklist_verified_by != "") {
                                                            $checklist_verified_Info = \App\Models\Admin::select('first_name')->where('id', $fetch->checklist_verified_by)->first();
                                                            $checklist_verified_by = $checklist_verified_Info->first_name;
                                                        } else {
                                                            $checklist_verified_by = 'N/A';
                                                        }

                                                        if( isset($fetch->checklist_verified_at) && $fetch->checklist_verified_at != "") {
                                                            $checklist_verified_at = date('d/m/Y', strtotime($fetch->checklist_verified_at));
                                                        } else {
                                                            $checklist_verified_at = 'N/A';
                                                        }*/
                                                        ?>
                                                        <tr class="drow" id="id_{{$fetch->id}}">
                                                            <!--<td><?php //echo $notuseKey+1;?></td>-->
                                                            <td style="white-space: initial;"><?php echo $fetch->checklist; ?></td>
                                                            <td style="white-space: initial;">
                                                                <?php
                                                                    echo $admin->first_name. "<br>";
                                                                    echo date('d/m/Y', strtotime($fetch->created_at));
                                                                ?>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                                                    <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                                                        <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $fetch->myfile; ?>">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-notuseddocumentlist')">
                                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                            </a>
                                                                        <?php } else {  //For old file upload
                                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                            $myawsfile = $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile;
                                                                            ?>
                                                                            <!--<a target="_blank" class="dropdown-item" href="<?php //echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">
                                                                                <i class="fas fa-file-image"></i> <span><?php //echo $fetch->file_name; ?><?php //echo '.'.$fetch->filetype; ?></span>
                                                                            </a>-->
                                                                      
                                                                            <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-notuseddocumentlist')">
                                                                                <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                            </a>
                                                                        <?php } ?>
                                                                    </div>
                                                                <?php
                                                                }
                                                                else
                                                                {
                                                                    echo "N/A";
                                                                }?>
                                                            </td>
                                                            <!--<td id="docverifiedby_<?php //echo $fetch->id;?>">
                                                                <?php
                                                                //echo $checklist_verified_by. "<br>";
                                                                //echo $checklist_verified_at;
                                                                ?>
                                                            </td>-->
                                                            <td>
                                                                <div class="dropdown d-inline">
                                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                    <div class="dropdown-menu">
                                                                        <?php
                                                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                        ?>
                                                                        <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                                        <?php } else {  //For old file upload ?>
                                                                            <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                                        <?php } ?>

                                                                        <a data-id="{{$fetch->id}}" class="dropdown-item backtodoc" data-doctype="documents" data-href="backtodoc" href="javascript:;">Back To Document</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
												    <?php
                                                    } //end foreach?>
												</tbody>
                                            </table>
										</div>
									</div>
                                  
                                    <!-- Container for File Preview -->
                                    <div class="col-5 col-md-5 col-lg-5 file-preview-container preview-container-notuseddocumentlist">
                                        <p style="color:#000;">Click on a file to preview it here.</p>
                                    </div>
                                </div>



								{{-- Appointments tab removed - Appointment model deleted --}}
                                
								<div class="tab-pane fade {{ $activeTab === 'noteterm' ? 'show active' : '' }}" id="noteterm" role="tabpanel" aria-labelledby="noteterm-tab">
									<div class="card-header-action text-end" style="padding-bottom:15px;">

									</div>
									<div class="note_term_list">
									<?php
									$notelist = \App\Models\Note::where('client_id', $fetchedData->id)->whereNull('assigned_to')->whereNull('task_group')->where('type', 'client')->orderby('pin', 'DESC')->orderBy('created_at', 'DESC')->get();
									//dd($notelist);
                                    foreach($notelist as $list){
										$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $list->user_id)->first();//dd($admin);
										$color = \App\Models\Team::select('color')->where('id',$admin->team)->first();

									?>
										<div class="note_col" id="note_id_{{$list->id}}">
                                            <div class="note_content">
											    <h4><a <?php if($color){ ?>style="color: #fff!important;"<?php } ?> class="viewnote" data-id="{{$list->id}}" href="javascript:;">{{ @$list->title == "" ? config('constants.empty') : str_limit(@$list->title, '19', '...') }}</a></h4>
											<?php if($list->pin == 1){
									?><div class="pined_note"><i class="fa fa-thumbtack"></i></i></div><?php } ?>
											</div>
											<div class="extra_content">
											     @if(!empty($list->description))
                                                    @php
                                                        $description = $list->description;
                                                    @endphp

                                                    @if(strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false)
                                                        <p>{!! htmlentities($description) !!}</p>
                                                    @else
                                                        <p>{!! $description !!}</p>
                                                    @endif
                                                @endif

                                                <?php if( isset($list->mobile_number) && $list->mobile_number != ""){ ?>
                                                    <p>{{ @$list->mobile_number }}</p>
                                                <?php }?>

												<div class="left">
													<div class="author">
														<a href="{{URL::to('/users/view/'.$admin->id)}}">{{substr($admin->first_name, 0, 1)}}</a>
													</div>
													<div class="note_modify">
														<small>Last Modified <span>{{date('d/m/Y h:i A', strtotime($list->updated_at))}}</span></small>
														{{$admin->first_name}}	 {{$admin->last_name}}
													</div>
												</div>
												<div class="right">
													<div class="dropdown d-inline dropdown_ellipsis_icon">
														<a class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
														<div class="dropdown-menu">
															<a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
                                                            @if(Auth::user()->role == 1)
															<a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                                            @endif
															<?php if($list->pin == 1){
                                                            ?>
                                                            	<a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >UnPin</a>
                                                            <?php
                                                            }else{ ?>
                                                                <a data-id="<?php echo $list->id; ?>"  class="dropdown-item pinnote" href="javascript:;" >Pin</a>
                                                            <?php } ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
									</div>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane fade {{ $activeTab === 'accounts' ? 'show active' : '' }}" id="accounts" role="tabpanel" aria-labelledby="accounts-tab">
									<div class="row">
										<div class="col-md-12 text-end">

                                            <a class="btn btn-primary createclientreceipt" href="javascript:;" role="button"  style="margin-right:5px !important;">Create Client Receipt</a>


											<div class="cus_invice_btn dropdown d-inline">
												<a href="#" data-bs-toggle="dropdown" class="nav-link nav-link-lg message-toggle btn btn-outline-primary">Create Invoice <i class="fa fa-angle-down"></i></a>
												<div class="dropdown-menu">
													<a href="javascript:;" class="dropdown-item opencommissioninvoice">
														Commission Invoice
													</a>
													<a href="javascript:;" class="dropdown-item opengeneralinvoice">
														General Invoice
													</a>
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="table-responsive">

                                      	<caption>Client Receipts</caption>
                                        <table border="1" style="margin-bottom:0rem !important;" class="table text_wrap table-striped table-hover table-md vertical_align">
                                            <thead>
                                                <tr>
                                                    <th>Trans. Date</th>
                                                    <th>Entry Date</th>
                                                    <th>Trans. No</th>
                                                    <th>Payment Method</th>
                                                    <th>Description</th>
                                                    <th>Deposit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="productitemList">
                                                <?php
                                                $receipts_lists = DB::table('account_client_receipts')->where('client_id',$fetchedData->id)->where('receipt_type',1)->get();
                                                //dd($receipts_lists);
                                                if(!empty($receipts_lists) && count($receipts_lists)>0 )
                                                {
                                                    $total_deposit_amount = 0.00;
                                                    foreach($receipts_lists as $rec_list=>$rec_val)
                                                    {

                                                ?>
                                                <tr  id="TrRow_<?php echo $rec_val->id;?>">
                                                    <td>
                                                        <?php echo $rec_val->trans_date;?>

                                                        <?php
                                                        if(isset($rec_val->uploaded_doc_id) && $rec_val->uploaded_doc_id >0){
                                                            $client_info = DB::table('admins')->select('id','client_id')->where('id',$rec_val->client_id)->first();

                                                        	$client_doc_list = DB::table('documents')->select('id','myfile','client_id','doc_type','myfile_key')->where('id',$rec_val->uploaded_doc_id)->first();
                                                            if($client_doc_list){
                                                                if( isset($client_doc_list->myfile_key) && $client_doc_list->myfile_key != "") {
                                                                    $awsUrl = $client_doc_list->myfile;
                                                                } else { 
                                                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                    $awsUrl = $url.$client_info->client_id.'/'.$client_doc_list->doc_type.'/'.$client_doc_list->myfile; 
                                                                }
                                                                ?>
                                                                <a target="_blank" class="link-primary" href="<?php echo $awsUrl;?>"><i class="fas fa-file-pdf"></i></a>
                                                            <?php
                                                            }
                                                        } ?>
                                                    </td>
                                                    <td><?php echo $rec_val->entry_date;?></td>
                                                    <td><?php echo $rec_val->trans_no;?></td>
                                                    <td><?php echo $rec_val->payment_method;?></td>
                                                    <td><?php echo $rec_val->description;?></td>
                                                    <td>
                                                        <?php echo "$".$rec_val->deposit_amount;?>
                                                        <a target="_blank" class="link-primary" href="{{URL::to('/clients/printpreview')}}/{{$rec_val->id}}"><i class="fa fa-print" aria-hidden="true"></i></a>
                                                       <?php
                                                        if( isset($rec_val->validate_receipt) && $rec_val->validate_receipt != 1){
                                                        ?>
                                              			<a class="link-primary updateclientreceipt" href="javascript:;" data-id="<?php echo $rec_val->id;?>">
                                                          <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                       <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                    $total_deposit_amount += $rec_val->deposit_amount;
                                                } //end foreach
                                                ?>

                                                <tr class="lastRow">
                                                    <td colspan="5" style="text-align:right;">Totals</td>
                                                    <td class="totDepoAmTillNow"><?php echo "$".$total_deposit_amount;?></td>
                                                </tr>
                                            <?php } else { ?>
                                                <!--<tr class="norecord"><td colspan="5">No Record Found</td></tr>-->
                                                <tr class="lastRow">
                                                    <td colspan="5" style="text-align:right;">Totals</td>
                                                    <td class="totDepoAmTillNow"><?php echo "$0";?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                        <br/>

                                        <br/>

										<table class="table invoicetable text_wrap">
											<thead>
												<tr>
													<th>Invoice No.</th>
													<th>Issue Date</th>
													<th>Service</th>
													<th>Invoice Amount</th>
													<th>Discount Given</th>
													<th>Income Shared</th>
													<th>Status</th>
													<th></th>
												</tr>
											</thead>
											<tbody class="tdata invoicedatalist">
												<?php
												$invoicelists = \App\Models\Invoice::where('client_id',$fetchedData->id)->orderby('created_at','DESC')->get();
												foreach($invoicelists as $invoicelist){
													if($invoicelist->type == 3){
														$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
													}else{
														$applicationdata = \App\Models\Application::where('id', $invoicelist->application_id)->first();
														$workflowdaa = \App\Models\Workflow::where('id', $invoicelist->application_id)->first();
														$partnerdata = \App\Models\Partner::where('id', @$applicationdata->partner_id)->first();
													}
													$invoiceitemdetails = \App\Models\InvoiceDetail::where('invoice_id', $invoicelist->id)->orderby('id','ASC')->get();
													$netamount = 0;
													$coom_amt = 0;
													$total_fee = 0;
													foreach($invoiceitemdetails as $invoiceitemdetail){
														$netamount += $invoiceitemdetail->netamount;
														$coom_amt += $invoiceitemdetail->comm_amt;
														$total_fee += $invoiceitemdetail->total_fee;
													}

													$paymentdetails = \App\Models\InvoicePayment::where('invoice_id', $invoicelist->id)->orderby('created_at', 'DESC')->get();
													$amount_rec = 0;
													foreach($paymentdetails as $paymentdetail){
														$amount_rec += $paymentdetail->amount_rec;
													}
													if($invoicelist->type == 1){
														$totaldue = $total_fee - $coom_amt;
													} if($invoicelist->type == 2){
														$totaldue = $netamount - $amount_rec;
													}else{
														$totaldue = $netamount - $amount_rec;
													}


												?>
												<tr id="iid_{{$invoicelist->id}}">
													<td>{{$invoicelist->id}}</td>
													<td>{{$invoicelist->invoice_date}}
													<?php if($invoicelist->type == 1){
														$rtype = 'Net Claim';
													}else if($invoicelist->type == 2){
														$rtype = 'Gross Claim';
													}else{
														$rtype = 'General';
													} ?>
													<span title="{{$rtype}}" class="ui label zippyLabel">{{$rtype}}</span></td>
													<td>{{@$workflowdaa->name}}<br>{{@$partnerdata->partner_name}}</td>
													<td>AUD {{$invoicelist->net_fee_rec}}</td>
													<td>{{$invoicelist->discount}}</td>
													<td>-</td>
													<td>
													@if($invoicelist->status == 1)
														<span class="ag-label--circular" style="color: #6777ef" >Paid</span></td>
													@else
														<span class="ag-label--circular" style="color: #ed5a5a" >UnPaid</span></td>
													@endif
													<td>
														<div class="dropdown d-inline">
															<button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
															<div class="dropdown-menu">
																<a class="dropdown-item has-icon" href="#">Send Email</a>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('invoice/view/')}}/{{$invoicelist->id}}">View</a>
																<?php if($invoicelist->status == 0){ ?>
																<a target="_blank" class="dropdown-item has-icon" href="{{URL::to('invoice/edit/')}}/{{$invoicelist->id}}">Edit</a>
																<a data-netamount="{{$netamount}}" data-dueamount="{{$totaldue}}" data-invoiceid="{{$invoicelist->id}}" class="dropdown-item has-icon addpaymentmodal" href="javascript:;"> Make Payment</a>
																<?php } ?>
															</div>
														</div>
													</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="tab-pane fade {{ $activeTab === 'conversations' ? 'show active' : '' }}" id="conversations" role="tabpanel" aria-labelledby="conversations-tab">
									<div class="conversation_tabs">
										<ul class="nav nav-pills round_tabs" id="client_tabs" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" data-bs-toggle="tab" id="email-tab" href="#email" role="tab" aria-controls="email" aria-selected="true">Email</a>
											</li>

										</ul>
										<div class="tab-content" id="conversationContent">

											<div class="tab-pane fade show active" id="email" role="tabpanel" aria-labelledby="email-tab">
												<div class="row">
													<div class="col-md-12" style="text-align: right;    margin-bottom: 10px;">
														<a class="btn btn-outline-primary btn-sm uploadmail"  href="javascript:;" >Upload Mail</a>
													</div>
												</div>
												<ul class="nav nav-pills round_tabs" id="client_mail_tabs" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" data-bs-toggle="tab" id="sent-tab" href="#sent" role="tab" aria-controls="sent" aria-selected="false">Sent</a>
													</li>
													<li class="nav-item">
														<a class="nav-link " data-bs-toggle="tab" id="inbox-tab" href="#inbox" role="tab" aria-controls="inbox" aria-selected="true">Inbox</a>
													</li>
												</ul>
										<div class="tab-content" id="conversationContent">
										<div class="tab-pane fade" id="inbox" role="tabpanel" aria-labelledby="inbox-tab" style="/*max-height: 1443px;*/overflow-y: auto;overflow-x: hidden;">

												<?php

											$mailreports = \App\Models\MailReport::where('client_id',$fetchedData->id)->where('type','client')->where('mail_type',1)->orderby('created_at', 'DESC')->get();

											foreach($mailreports as $mailreport){

											?>
												<div class="conversation_list" style="max-height: 200px;overflow-y: auto;overflow-x: hidden;margin-bottom: 10px;border-bottom: 1px solid rgba(34, 36, 38, .15);">
													<div class="conversa_item">
														<div class="ds_flex">
															<div class="title">
																<span>{{@$mailreport->subject}}</span>
															</div>
															<div class="conver_action">
																<div class="date">
																	<span>{{date('h:i A', strtotime(@$mailreport->created_at))}}</span>
																</div>

															</div>
														</div>
														<div class="email_info">
															<div class="avatar_img">
																<span>{{substr(@$mailreport->from_mail, 0, 1)}}</span>
															</div>
															<div class="email_content">
																<span class="email_label">Sent by:</span>
																<span class="email_sentby"><strong>{{@$mailreport->from_mail}}</strong> </span>
																<span class="label success">Delivered</span>
																<span class="span_desc">
																	<span class="email_label">Sent To</span>
																	<span class="email_sentby"><i class="fa fa-angle-left"></i>{{@$mailreport->to_mail}}<i class="fa fa-angle-right"></i></span>
																</span>
															</div>
														</div>
														<div class="divider"></div>
														<div class="email_desc">
														 @if(@$mailreport->attachments != '')
														 <?php
														/*  $decodeatta = json_decode($mailreport->attachments);
														 if(!empty($decodeatta)){
														 ?>
														    <div class="attachments">
														        <ul style="list-style: none;">
										@foreach($decodeatta as $attaa)
										    <li style="display:inline-block;padding: 0px 11px;
											border-radius: 4px;
											box-shadow: 0 3px 8px 0 rgb(0 0 0 / 8%), 0 1px 2px 0 rgb(0 0 0 / 10%);"><a href="<?php echo asset('checklists/'.$attaa->file_url); ?>" target="_blank">{{$attaa->file_name}}</a></li>
																				@endforeach
										</ul>
														    </div>
														    	<?php } */ ?>
											@endif
														{!!$mailreport->message!!}
														</div>
														<div class="divider"></div>
														<?php
														/* if($mailreport->reciept_id != ''){
															if(\App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->exists()){
																$invpayment = \App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->first();
														?>
														<div class="email_attachment">
															<span class="attach_label"><i class="fa fa-link"></i> Attachments:</span>
															<div class="attach_file_list">
																<div class="attach_col">
																	<a href="{{URL::to('payment/view/')}}/{{base64_encode(convert_uuencode(@$invpayment->id))}}">receipt_{{$invpayment->id}}.pdf</a>
																</div>
															</div>
														</div>
														<?php } ?>
														<?php } */ ?>
													</div>
												</div>
											<?php } ?>
										</div>
										<div class="tab-pane fade  show active" id="sent" role="tabpanel" aria-labelledby="sent-tab" style="/*max-height: 1443px;*/overflow-y: auto;overflow-x: hidden;">
											<?php

											$mailreports = \App\Models\MailReport::whereRaw('? = ANY(string_to_array(to_mail, \',\'))', [$fetchedData->id])->where('type','client')->where('mail_type',0)->orderby('created_at', 'DESC')->get();

											foreach($mailreports as $mailreport){
												$admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $mailreport->user_id)->first();

												$client = \App\Models\Admin::select('id', 'first_name','email')->Where('id', $fetchedData->id)->first();
												$subject = str_replace('{Client First Name}',$client->first_name, $mailreport->subject);
												$message = $mailreport->message;
												$message = str_replace('{Client First Name}',$client->first_name, $message);
												$message = str_replace('{Client Assignee Name}',$client->first_name, $message);
												$message = str_replace('{Company Name}',Auth::user()->company_name, $message);
											?>
												<div class="conversation_list" style="max-height: 200px;overflow-y: auto;overflow-x: hidden;margin-bottom: 10px;border-bottom: 1px solid rgba(34, 36, 38, .15);">
													<div class="conversa_item">
														<div class="ds_flex">
															<div class="title">
																<span>{{$subject}}</span>
															</div>
															<div class="conver_action">
																<div class="date">
																	<span>{{date('h:i A', strtotime($mailreport->created_at))}}</span>
																</div>
																<div class="conver_link">
																	<a datamailid="{{$mailreport->id}}" datasubject="{{$subject}}" class="create_note" datatype="mailnote" href="javascript:;" ><i class="fas fa-file-alt"></i></a>
																</div>
															</div>
														</div>
														<div class="email_info">
															<div class="avatar_img">
																<span>{{substr($admin->first_name, 0, 1)}}</span>
															</div>
															<div class="email_content">
																<span class="email_label">Sent by:</span>
																<span class="email_sentby"><strong>{{@$admin->first_name}}</strong> [{{$mailreport->from_mail}}]</span>
																<span class="label success">Delivered</span>
																<span class="span_desc">
																	<span class="email_label">Sent To</span>
																	<span class="email_sentby"><i class="fa fa-angle-left"></i>{{$client->email}}<i class="fa fa-angle-right"></i></span>
																</span>
															</div>
														</div>
														<div class="divider"></div>
														<div class="email_desc">
														 @if($mailreport->attachments != '')
														 <?php
														 $decodeatta = json_decode($mailreport->attachments);
														 if(!empty($decodeatta)){
														 ?>
														    <div class="attachments">
														        <ul style="list-style: none;">
											@foreach($decodeatta as $attaa)
												<li style="display:inline-block;padding: 0px 11px;
												border-radius: 4px;
												box-shadow: 0 3px 8px 0 rgb(0 0 0 / 8%), 0 1px 2px 0 rgb(0 0 0 / 10%);"><a href="<?php echo asset('checklists/'.$attaa->file_url); ?>" target="_blank">{{$attaa->file_name}}</a></li>
											@endforeach
										</ul>
														    </div>
														    	<?php } ?>
											@endif
														{!!$message!!}
														</div>
														<div class="divider"></div>
														<?php
														if($mailreport->reciept_id != ''){
															if(\App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->exists()){
																$invpayment = \App\Models\InvoicePayment::where('id',$mailreport->reciept_id)->first();
														?>
														<div class="email_attachment">
															<span class="attach_label"><i class="fa fa-link"></i> Attachments:</span>
															<div class="attach_file_list">
																<div class="attach_col">
																	<a href="{{URL::to('payment/view/')}}/{{base64_encode(convert_uuencode(@$invpayment->id))}}">receipt_{{$invpayment->id}}.pdf</a>
																</div>
															</div>
														</div>
														<?php } ?>
														<?php } ?>
													</div>
												</div>
											<?php } ?>
											</div>
											</div>
											</div>
										</div>
									</div>
								</div>
							<!--<div class="tab-pane fade" id="other_info" role="tabpanel" aria-labelledby="other_info-tab">
								<span>other_info</span>
							</div>-->
						</div> <!-- end tab-content -->
					</div> <!-- end card-body -->
				</div> <!-- end card -->
			</div> <!-- end right_section -->
		</div> <!-- end bottom-row-container -->
	</div> <!-- end client-detail-container -->
	</div> <!-- end section-body -->
</section>
</div> <!-- end main-content -->

@include('Admin/clients/addclientmodal')
@include('Admin/clients/editclientmodal')

<div id="emailmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="clientModalLabel">Compose Email</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmail" action="{{URL::to('/sendmail')}}" autocomplete="off" enctype="multipart/form-data">
				@csrf
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_from">From <span class="span_req">*</span></label>
								<select class="form-control" name="email_from" data-valid="required">
                                    <option value="">Select From</option>
									<?php
									$emails = \App\Models\Email::select('email')->where('status', 1)->get();
									foreach($emails as $nemail){
										?>
											<option value="<?php echo $nemail->email; ?>"><?php echo $nemail->email; ?></option>
										<?php
									}

									?>
								</select>
								@if ($errors->has('email_from'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_from') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_to">To <span class="span_req">*</span></label>
								<select data-valid="required" class="js-data-example-ajax" name="email_to[]"></select>

								@if ($errors->has('email_to'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_to') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="email_cc">CC </label>
								<select data-valid="" class="js-data-example-ajaxccd" name="email_cc[]"></select>

								@if ($errors->has('email_cc'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('email_cc') }}</strong>
									</span>
								@endif
							</div>
						</div>

						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="template">Templates </label>
                                 <?php
                                // PostgreSQL doesn't accept empty strings for integer columns - check before querying
                                // Handle comma-separated assignee values (same pattern as lines 647-653)
                                $assignee = null;
                                if(!empty(@$fetchedData->assignee) && @$fetchedData->assignee !== '') {
                                    // Check if assignee contains multiple IDs (comma-separated)
                                    if(Str::contains($fetchedData->assignee, ',')){
                                        // Get the first assignee ID from comma-separated list
                                        $assigneeUArr = explode(",", $fetchedData->assignee);
                                        $firstAssigneeId = trim($assigneeUArr[0]);
                                        if(!empty($firstAssigneeId) && is_numeric($firstAssigneeId)) {
                                            $assignee = \App\Models\Admin::select('first_name')->where('id', $firstAssigneeId)->first();
                                        }
                                    } else {
                                        // Single assignee ID
                                        $assigneeId = trim($fetchedData->assignee);
                                        if(!empty($assigneeId) && is_numeric($assigneeId)) {
                                            $assignee = \App\Models\Admin::select('first_name')->where('id', $assigneeId)->first();
                                        }
                                    }
                                }
                                if($assignee){
                                    $clientAssigneeName = $assignee->first_name;
                                } else {
                                    $clientAssigneeName = 'NA';
                                }
                                ?>
								<select data-valid="" class="form-control select2 selecttemplate" name="template" data-clientid="{{@$fetchedData->id}}" data-clientfirstname="{{@$fetchedData->first_name}}" data-clientvisaExpiry="{{@$fetchedData->visaExpiry}}" data-clientreference_number="{{@$fetchedData->client_id}}" data-clientassignee_name="{{@$clientAssigneeName}}">
									<option value="">Select</option>
									@foreach(\App\Models\CrmEmailTemplate::orderBy('id', 'desc')->get() as $list)
										<option value="{{$list->id}}">{{$list->name}}</option>
									@endforeach
								</select>

							</div>
						</div>
                        <!-- Inline ChatGPT Section (hidden by default) -->
                        <div id="chatGptSection" class="collapse mt-3 col-9 col-md-9 col-lg-9">
                            <div class="card card-body">
                                <div class="form-group">
                                    <label for="chatGptInput">Enter your message to enhance:</label>
                                    <textarea class="form-control" id="chatGptInput" rows="5" placeholder="Type your message here..."></textarea>
                                </div>
                                <div class="mt-2 text-end">
                                    <button type="button" class="btn btn-primary" id="enhanceMessageBtn">Enhance</button>
                                    <button type="button" class="btn btn-secondary" id="chatGptClose">Close</button>
                                </div>
                            </div>
                        </div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="subject">Subject <span class="span_req">*</span>
                                <button type="button" class="btn btn-info" id="chatGptToggle">ChatGPT Enhance</button>  
                              </label>
								{!! Form::text('subject', '', array('id'=>'compose_email_subject','class' => 'form-control selectedsubject', 'data-valid'=>'required', 'autocomplete'=>'off','placeholder'=>'Enter Subject' ))  !!}
								@if ($errors->has('subject'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('subject') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" id="compose_email_message" name="message"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						     <div class="form-group">
						        <label>Attachment</label>
						        <input type="file" name="attach[]" class="form-control" multiple>
						     </div>
						</div>
                      
                         <div class="col-12 col-md-12 col-lg-12">
                            <div class="composeemail-tab">
                                <ul class="nav nav-pills round_tabs" id="composeemails-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="pill" id="composechecklist-tab" href="#composechecklist" role="tab" aria-controls="composechecklist" aria-selected="true">Checklist</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="pill" id="composedocument-tab" href="#composedocument" role="tab" aria-controls="composedocument" aria-selected="false">Document List</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="composeemailContent">
                                    <div class="tab-pane fade show active" id="composechecklist" role="tabpanel" aria-labelledby="composechecklist-tab">
                                        <div class="table-responsive uploadchecklists">

                                            <table id="mychecklist-datatable" class="table text_wrap table-2">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th style="white-space: initial;">File Name</th>
                                                        <th style="white-space: initial;">File</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(\App\Models\UploadChecklist::all() as $uclist)
                                                    <tr>
                                                        <td><input type="checkbox" name="checklistfile[]" value="{{$uclist->id}}" {{ old('checklistfile') && in_array($uclist->id, old('checklistfile', [])) ? 'checked' : '' }}></td>
                                                        <td style="white-space: initial;">{{$uclist->name}}</td>
                                                        <td style="white-space: initial;"><a target="_blank" href="{{ asset('checklists/'.$uclist->file) }}">{{$uclist->name}}</a></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="composedocument" role="tabpanel" aria-labelledby="composedocument-tab">
                                        <?php echo $fetchedData->id;?>
                                        <table id="mydocumentlist-datatable" class="table text_wrap table-2">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th style="white-space: initial;">File Name</th>
                                                    <th>Document Type</th>
                                                    <th style="white-space: initial;">File</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(\App\Models\Document::where('client_id',$fetchedData->id)->where('type','client')->whereIn('doc_type', ['education', 'migration', 'documents'])->whereNull('not_used_doc')->orderby('created_at', 'DESC')->get() as $composedoclist)
                                                <tr>
                                                    <td><input type="checkbox" name="checklistfile_document[]" value="{{$composedoclist->id}}" {{ old('checklistfile_document') && in_array($composedoclist->id, old('checklistfile_document', [])) ? 'checked' : '' }}></td>
                                                    <td style="white-space: initial;">{{$composedoclist->file_name}}</td>
                                                    <td>
                                                        <?php
                                                        $docTypes = [
                                                            'education' => 'Education',
                                                            'migration' => 'Migration',
                                                            'documents' => 'Document'
                                                        ];

                                                        echo isset($composedoclist->doc_type)
                                                            ? ($docTypes[$composedoclist->doc_type] ?? 'N/A')
                                                            : 'N/A';
                                                        ?>
                                                    </td>

                                                    <td style="white-space: initial;">
                                                        <?php
                                                        if( isset($composedoclist->doc_type) && $composedoclist->doc_type != "" )
                                                        {
                                                            if( $composedoclist->doc_type == "education" || $composedoclist->doc_type == "migration" ){ ?>
                                                                <a target="_blank" class="dropdown-item" href="{{asset('img/documents')}}/{{$composedoclist->myfile}}">{{$composedoclist->file_name}}</a>
                                                            <?php
                                                            }
                                                            else if( $composedoclist->doc_type == "documents")
                                                            {
                                                                if( isset($composedoclist->myfile_key) && $composedoclist->myfile_key != "")
                                                                { ?>
                                                                    <a target="_blank" href="<?php echo $composedoclist->myfile;?>">{{$composedoclist->file_name}}</a>
                                                                <?php
                                                                }
                                                                else
                                                                {
                                                                    $clientInfo = \App\Models\Admin::where('id',$fetchedData->id)->select('client_id')->first();
                                                                    if($clientInfo){
                                                                        $client_unique_id = $clientInfo->client_id;
                                                                    } else {
                                                                        $client_unique_id = 'N/A';
                                                                    }
                                                                    $doc_type = $composedoclist->doc_type;
                                                                    $myfile = $composedoclist->myfile;

                                                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                    $composedoclistUrl = $url.$client_unique_id.'/'.$doc_type.'/'.$myfile; //dd($awsUrl);

                                                                    ?>
                                                                    <a target="_blank" href="<?php echo $composedoclistUrl;?>"><?php echo $composedoclist->file_name;?></a>
                                                                <?php
                                                                }
                                                            }
                                                        } ?>
                                                    </td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
						
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmail')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<!-- Send Message-->
<div id="sendmsgmodal"  data-backdrop="static" data-keyboard="false" class="modal fade custom_modal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="messageModalLabel">Send Message</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" name="sendmsg" id="sendmsg" action="{{URL::to('/sendmsg')}}" autocomplete="off" enctype="multipart/form-data">
				    @csrf
                    <input type="hidden" name="client_id" id="sendmsg_client_id" value="">
                    <input type="hidden" name="vtype" value="client">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="message">Message <span class="span_req">*</span></label>
								<textarea class="summernote-simple selectedmessage" name="message" data-valid="required"></textarea>
								@if ($errors->has('message'))
									<span class="custom-error" role="alert">
										<strong>{{ @$errors->first('message') }}</strong>
									</span>
								@endif
							</div>
						</div>
                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sendmsg')" type="button" class="btn btn-primary">Send</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<div class="modal fade  custom_modal" id="interest_service_view" tabindex="-1" role="dialog" aria-labelledby="interest_serviceModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content showinterestedservice">

		</div>
	</div>
</div>

<div id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this note?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmNotUseDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this document in Not Use Tab?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmBackToDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to send this in document Tab again?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Send</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmDocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to verify this doc?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Verify</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmLogModal" tabindex="-1" role="dialog" aria-labelledby="confirmLogModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to delete this log?</h4>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger accept">Delete</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div id="confirmcompleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Do you want to complete the Application?</h4>
				<button  data-id="" type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptapplication">Complete</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>


<div id="confirmpublishdocModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="false" class="modal fade" >
	<div class="modal-dialog">
		<div class="modal-content popUp">
			<div class="modal-body text-center">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn-close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title text-center message col-v-5">Publish Document?</h4>
				<h5 class="">Publishing documents will allow client to access from client portal , Are you sure you want to continue ?</h5>
				<button type="submit" style="margin-top: 40px;" class="button btn btn-danger acceptpublishdoc">Publish Anyway</button>
				<button type="button" style="margin-top: 40px;" data-bs-dismiss="modal" class="button btn btn-secondary cancel">Cancel</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcast')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="application_ownership" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Application Ownership Ratio</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/application/application_ownership')}}" name="xapplication_ownership" id="xapplication_ownership" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="mapp_id" id="mapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sus_agent"> </label>
								<input type="number" max="100" min="0" step="0.01" class="form-control ration" name="ratio">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('xapplication_ownership')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="superagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Super Agent</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/application/spagent_application')}}" name="spagent_application" id="spagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="siapp_id" id="siapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="super_agent">Super Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control super_agent" id="super_agent" name="super_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('? = ANY(string_to_array(agent_type, \',\'))', ['Super Agent'])->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('spagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="subagent_application" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Select Sub Agent</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/application/sbagent_application')}}" name="sbagent_application" id="sbagent_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="sbapp_id" id="sbapp_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="sub_agent">Sub Agent <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control sub_agent" id="sub_agent" name="sub_agent">
									<option value="">Please Select</option>
									<?php $sagents = \App\Models\Agent::whereRaw('? = ANY(string_to_array(agent_type, \',\'))', ['Sub Agent'])->where('is_acrchived',0)->get(); ?>
									@foreach($sagents as $sa)
										<option value="{{$sa->id}}">{{$sa->full_name}} {{$sa->email}}</option>
									@endforeach
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('sbagent_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="tags_clients" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Tags</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/save_tag')}}" name="stags_application" id="stags_application" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="client_id" id="tags_client_id" value="">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="tagname_input">Tags <span class="span_req">*</span></label>
								<input
									type="text"
									id="tagname_input"
									name="tagname"
									class="form-control"
									data-valid="required"
									placeholder="e.g. VIP, Follow up, IELTS"
									value="{{ trim($fetchedData->tagname ?? '') }}"
								/>
								<small class="form-text text-muted">Separate tags with commas.</small>
                            </div>
						</div>

						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('stags_application')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="new_fee_option" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabel">Fee Option</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfee">

			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="new_fee_option_latest" tabindex="-1" role="dialog" aria-labelledby="feeoptionModalLabelLatest" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="feeoptionModalLabelLatest">Other Fee Option</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body showproductfee_latest">

			</div>
		</div>
	</div>



<div class="modal fade custom_modal" id="application_opensaleforcast" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/application/saleforcast')}}" name="saleforcast" id="saleforcast" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcast')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="modal fade custom_modal" id="application_opensaleforcastservice" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appliationModalLabel">Sales Forecast</h5>
				<button type="button" class="close closeservmodal" >
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="post" action="{{URL::to('/application/saleforcastservice')}}" name="saleforcastservice" id="saleforcastservice" autocomplete="off" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="fapp_id" id="fapp_id" value="">
					<div class="row">
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Client Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="client_revenue" name="client_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Partner Revenue</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="partner_revenue" name="partner_revenue">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="sus_agent">Discounts</label>
								<input type="number" value="0.00" max="100" min="0" step="0.01" class="form-control " id="discounts" name="discounts">
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('saleforcastservice')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade custom_modal" id="serviceTaken" tabindex="-1" role="dialog" aria-labelledby="create_interestModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="interestModalLabel">Service Taken</h5>

				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <form method="post" action="{{URL::to('/client/createservicetaken')}}" name="createservicetaken" id="createservicetaken" autocomplete="off" enctype="multipart/form-data">
				@csrf
                    <input id="logged_client_id" name="logged_client_id"  type="hidden" value="<?php echo $fetchedData->id;?>">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">

							<div class="form-group">
								<label style="display:block;" for="service_type">Select Service Type:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Migration_inv" value="Migration" name="service_type" checked>
									<label class="form-check-label" for="Migration_inv">Migration</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="Eductaion_inv" value="Eductaion" name="service_type">
									<label class="form-check-label" for="Eductaion_inv">Eductaion</label>
								</div>
								<span class="custom-error service_type_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>

						<div class="col-12 col-md-12 col-lg-12 is_Migration_inv">
                            <div class="form-group">
								<label for="mig_ref_no">Reference No: <span class="span_req">*</span></label>
                                <input type="text" name="mig_ref_no" id="mig_ref_no" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_service">Service: <span class="span_req">*</span></label>
                                <input type="text" name="mig_service" id="mig_service" value="" class="form-control" data-valid="required">
                            </div>

                            <div class="form-group">
								<label for="mig_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="mig_notes" id="mig_notes" value="" class="form-control" data-valid="required">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12 is_Eductaion_inv" style="display:none;">
                            <div class="form-group">
								<label for="edu_course">Course: <span class="span_req">*</span></label>
                                <input type="text" name="edu_course" id="edu_course" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_college">College: <span class="span_req">*</span></label>
                                <input type="text" name="edu_college" id="edu_college" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_service_start_date">Service Start Date: <span class="span_req">*</span></label>
                                <input type="text" name="edu_service_start_date" id="edu_service_start_date" value="" class="form-control">
                            </div>

                            <div class="form-group">
								<label for="edu_notes">Notes: <span class="span_req">*</span></label>
                                <input type="text" name="edu_notes" id="edu_notes" value="" class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('createservicetaken')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script src="{{asset('js/popover.js')}}"></script>

@if($showAlert)
    <script>
        alert("Have u updated the following details - email address,current address,current visa,visa expiry,other fields? Pls update these details before forwarding this to anyone?");
    </script>
@endif

{{-- Configuration Script: Pass Blade variables to JavaScript --}}
<script>
    window.AppConfig = window.AppConfig || {};
    window.PageConfig = window.PageConfig || {};
    
    // Global Configuration
    AppConfig.csrf = '{{ csrf_token() }}';
    AppConfig.siteUrl = '{{ url("/") }}';
    AppConfig.urls = {
        siteUrl: '{{ url("/") }}',
        downloadDocument: '{{ url("/download-document") }}',
        deleteAction: '{{ url("/delete_action") }}',
        getActivities: '{{ url("/get-activities") }}',
        getNotes: '{{ url("/get-notes") }}',
        deleteActivityLog: '{{ url("/deleteactivitylog") }}',
        mailEnhance: '{{ route("clients.enhanceMessage") }}',
        isGReviewMailSent: '{{ url("/is_greview_mail_sent") }}',
        clientGetTopReceipt: '{{ url("/clients/getTopReceiptValInDB") }}',
        notPickedCall: '{{ url("/not-picked-call") }}',
        getDateTimeBackend: '{{ url("/getdatetimebackend") }}',
        getDisabledDateTime: '{{ url("/getdisableddatetime") }}',
        clientUpdateEmailVerified: '{{ url("/clients/update-email-verified") }}',
        clientChangeAssignee: '{{ url("/clients/change_assignee") }}',
        clientFollowup: '{{ url("/clients/followup/store") }}',
        pinNote: '{{ url("/pinnote") }}',
        pinActivityLog: '{{ url("/pinactivitylog") }}',
        getNoteDetail: '{{ url("/getnotedetail") }}',
        viewNoteDetail: '{{ url("/viewnotedetail") }}',
        viewApplicationNote: '{{ url("/viewapplicationnote") }}',
        getPartnerBranch: '{{ url("/getpartnerbranch") }}',
        getBranchProduct: '{{ url("/getbranchproduct") }}',
        clientGetRecipients: '{{ url("/clients/get-recipients") }}',
        changeClientStatus: '{{ url("/change-client-status") }}',
        getTemplates: '{{ url("/get-templates") }}',
        uploadDocument: '{{ url("/upload-document") }}',
        uploadAllDocument: '{{ url("/upload-alldocument") }}',
        convertApplication: '{{ url("/convertapplication") }}',
        getServices: '{{ url("/get-services") }}',
        getApplicationLists: '{{ url("/get-application-lists") }}',
        renameDoc: '{{ url("/renamedoc") }}',
        renameAllDoc: '{{ url("/renamealldoc") }}',
        renameChecklistDoc: '{{ url("/renamechecklistdoc") }}',
        getBranch: '{{ url("/getbranch") }}',
        clientUpdateSession: '{{ url("/clients/update-session-completed") }}',
        clientFetchContact: '{{ url("/clients/fetchClientContactNo") }}',
        sendMail: '{{ url("/sendmail") }}',
        clientGetReceiptInfo: '{{ url("/clients/getClientReceiptInfoById") }}',
        // NOTE: addScheduleInvoiceDetail removed - Invoice Schedule feature has been removed
        applicationChecklistUpload: '{{ url("/application/checklistupload") }}',
        getApplicationsLogs: '{{ url("/get-applications-logs") }}',
        getApplicationDetail: '{{ url("/getapplicationdetail") }}',
        updateApplicationDates: '{{ url("/application/updatedates") }}'
    };
    
    // Page-Specific Configuration
    PageConfig.clientId = {{ $fetchedData->id ?? 'null' }};
    PageConfig.clientName = '{{ $fetchedData->first_name ?? "" }}';
    PageConfig.clientType = 'client';
</script>

{{-- Common JavaScript Files (load first) --}}
<script src="{{ asset('js/common/config.js') }}"></script>
<script src="{{ asset('js/common/ajax-helpers.js') }}"></script>
<script src="{{ asset('js/common/utilities.js') }}"></script>
<script src="{{ asset('js/common/crud-operations.js') }}"></script>
<script src="{{ asset('js/common/activity-handlers.js') }}"></script>
<script src="{{ asset('js/common/document-handlers.js') }}"></script>
<script src="{{ asset('js/common/ui-components.js') }}"></script>

{{-- Page-Specific JavaScript (load last) --}}
<script src="{{ asset('js/pages/admin/client-detail.js') }}"></script>

<script>
    // Any remaining Blade-specific code that cannot be extracted goes here
    // Most functionality has been moved to external JS files
    
    // Keep URL in sync with active tab and honor ?tab= on load
    (function() {
        var tabList = document.getElementById('client_tabs');
        if (!tabList) {
            return;
        }

        var tabLinks = tabList.querySelectorAll('[data-bs-toggle="tab"][data-tab]');
        if (!tabLinks.length) {
            return;
        }

        var baseUrl = tabList.getAttribute('data-base-url');
        if (!baseUrl) {
            return;
        }
        var activeTabSlug = tabList.getAttribute('data-active-tab');
        var base = new URL(baseUrl, window.location.origin);
        var basePath = base.pathname.replace(/\/+$/, '');

        var params = new URLSearchParams(window.location.search);
        var initialTab = params.get('tab');
        if (initialTab) {
            var normalizedInitialTab = initialTab === 'noteterm' ? 'notestrm' : initialTab;
            var initialTrigger = tabList.querySelector('[data-tab="' + normalizedInitialTab + '"]');
            if (initialTrigger && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(initialTrigger).show();
            }
            var migratedUrl = new URL(window.location.href);
            migratedUrl.searchParams.delete('tab');
            migratedUrl.pathname = normalizedInitialTab === 'activities' ? basePath : basePath + '/' + normalizedInitialTab;
            history.replaceState(null, '', migratedUrl.toString());
        } else if (activeTabSlug) {
            var canonicalUrl = new URL(window.location.href);
            canonicalUrl.searchParams.delete('tab');
            canonicalUrl.pathname = activeTabSlug === 'activities' ? basePath : basePath + '/' + activeTabSlug;
            history.replaceState(null, '', canonicalUrl.toString());
        }

        tabLinks.forEach(function(link) {
            link.addEventListener('shown.bs.tab', function(event) {
                var tabValue = event.target.getAttribute('data-tab');
                if (!tabValue) {
                    return;
                }
                var url = new URL(window.location.href);
                url.searchParams.delete('tab');
                url.pathname = tabValue === 'activities' ? basePath : basePath + '/' + tabValue;
                history.replaceState(null, '', url.toString());
            });
        });
    })();

    // Initialize Bootstrap 5 dropdowns for Action buttons
    // This ensures all dropdown buttons work properly
    (function() {
        var dropdownInitAttempts = 0;
        var maxAttempts = 50; // 5 seconds max wait
        
        function initDropdowns() {
            dropdownInitAttempts++;
            
            // Check if Bootstrap is available
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                // Initialize all dropdown toggles that aren't already initialized
                var dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                var initializedCount = 0;
                
                dropdownToggles.forEach(function(element) {
                    // Check if dropdown is already initialized
                    if (!bootstrap.Dropdown.getInstance(element)) {
                        try {
                            new bootstrap.Dropdown(element);
                            initializedCount++;
                        } catch (e) {
                            console.warn('Failed to initialize dropdown:', e, element);
                        }
                    }
                });
                
                if (initializedCount > 0) {
                    console.log('Initialized ' + initializedCount + ' Bootstrap dropdown(s)');
                }
                
                // Setup mutation observer for dynamically added dropdowns
                if (!window.dropdownObserverSetup) {
                    window.dropdownObserverSetup = true;
                    
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.addedNodes.length > 0) {
                                mutation.addedNodes.forEach(function(node) {
                                    if (node.nodeType === 1) { // Element node
                                        // Check for dropdown toggles in the added node
                                        var dropdowns = node.querySelectorAll ? node.querySelectorAll('[data-bs-toggle="dropdown"]') : [];
                                        dropdowns.forEach(function(element) {
                                            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown && !bootstrap.Dropdown.getInstance(element)) {
                                                try {
                                                    new bootstrap.Dropdown(element);
                                                } catch (e) {
                                                    console.warn('Failed to initialize dynamic dropdown:', e);
                                                }
                                            }
                                        });
                                        
                                        // Also check if the node itself is a dropdown toggle
                                        if (node.hasAttribute && node.hasAttribute('data-bs-toggle') && node.getAttribute('data-bs-toggle') === 'dropdown') {
                                            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown && !bootstrap.Dropdown.getInstance(node)) {
                                                try {
                                                    new bootstrap.Dropdown(node);
                                                } catch (e) {
                                                    console.warn('Failed to initialize dynamic dropdown:', e);
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        });
                    });
                    
                    // Observe the document body for changes
                    observer.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                }
            } else if (dropdownInitAttempts < maxAttempts) {
                // Retry if Bootstrap isn't loaded yet
                setTimeout(initDropdowns, 100);
            } else {
                console.error('Bootstrap Dropdown not available after ' + maxAttempts + ' attempts');
            }
        }
        
        // Start initialization when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDropdowns);
        } else {
            // DOM is already ready
            initDropdowns();
        }
        
        // Also try after window load as a fallback
        window.addEventListener('load', function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                initDropdowns();
            }
        });
    })();

    // Activities Filter Functionality
    $(document).ready(function() {
        // Activity Type Button Click Handler (main buttons)
        $('.activity-type-btn:not(.dropdown-toggle)').on('click', function() {
            var type = $(this).data('type');
            
            // Remove active class from all buttons and dropdown items
            $('.activity-type-btn').removeClass('active');
            $('.activity-type-dropdown-item').removeClass('active');
            
            // Add active class to clicked button
            $(this).addClass('active');
            
            // Reset dropdown button text
            $('.activity-type-btn.dropdown-toggle').text('More...').removeClass('active');
            
            // Update hidden input
            $('#activity_type_input').val(type);
        });

        // Activity Type Dropdown Item Click Handler
        $(document).on('click', '.activity-type-dropdown-item', function(e) {
            e.preventDefault();
            var type = $(this).data('type');
            var label = $(this).text();
            
            // Remove active class from all buttons and dropdown items
            $('.activity-type-btn').removeClass('active');
            $('.activity-type-dropdown-item').removeClass('active');
            
            // Add active class to clicked dropdown item
            $(this).addClass('active');
            
            // Update dropdown button
            var $dropdownBtn = $('.activity-type-btn.dropdown-toggle');
            $dropdownBtn.text(label).addClass('active');
            
            // Update hidden input
            $('#activity_type_input').val(type);
        });

        // Initialize Date Pickers with Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr('.date-filter', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                altInput: false
            });
        } else {
            console.warn('Flatpickr is not available. Please ensure vendor-libs.js is loaded.');
        }

        // Auto-submit form on Enter key in search box
        $('#activity_search').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#activitiesFilterForm').submit();
            }
        });
    });

    // Add Interested Services Modal - Populate dropdowns based on selections
    // This is scoped to only affect the .add_interested_service modal to avoid breaking other functionality
    $(document).ready(function() {
        // Helper function to re-initialize Select2 after AJAX update
        function reinitSelect2($select, dropdownParent) {
            if (typeof $.fn.select2 === 'undefined') {
                return; // Select2 not available, skip initialization
            }
            try {
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                var select2Options = {
                    dropdownParent: dropdownParent || $select.closest('.modal')
                };
                $select.select2(select2Options);
            } catch(e) {
                console.warn('Select2 initialization failed:', e);
            }
        }

        // Handle Workflow change - Load Partners
        $(document).delegate('.add_interested_service #intrested_workflow', 'change', function(){
            var workflowId = $(this).val();
            var $modal = $(this).closest('.add_interested_service');
            var $partnerSelect = $modal.find('#intrested_partner');
            var $productSelect = $modal.find('#intrested_product');
            var $branchSelect = $modal.find('#intrested_branch');
            
            if(workflowId != ''){
                if($('.popuploader').length) {
                    $('.popuploader').show();
                }
                $.ajax({
                    url: '{{URL::to('/getpartner')}}',
                    type: 'GET',
                    data: {cat_id: workflowId},
                    success: function(response){
                        if($('.popuploader').length) {
                            $('.popuploader').hide();
                        }
                        $partnerSelect.html(response);
                        reinitSelect2($partnerSelect, $modal);
                        $partnerSelect.val('').trigger('change');
                        
                        $productSelect.html('<option value="">Please Select a Product</option>');
                        reinitSelect2($productSelect, $modal);
                        $productSelect.val('').trigger('change');
                        
                        $branchSelect.html('<option value="">Please Select a Branch</option>');
                        reinitSelect2($branchSelect, $modal);
                        $branchSelect.val('').trigger('change');
                    },
                    error: function(){
                        if($('.popuploader').length) {
                            $('.popuploader').hide();
                        }
                    }
                });
            } else {
                // Reset all dependent dropdowns if workflow is cleared
                $partnerSelect.html('<option value="">Please Select a Partner</option>');
                reinitSelect2($partnerSelect, $modal);
                $productSelect.html('<option value="">Please Select a Product</option>');
                reinitSelect2($productSelect, $modal);
                $branchSelect.html('<option value="">Please Select a Branch</option>');
                reinitSelect2($branchSelect, $modal);
            }
        });

        // Handle Partner change - Load Products
        $(document).delegate('.add_interested_service #intrested_partner', 'change', function(){
            var partnerId = $(this).val();
            var $modal = $(this).closest('.add_interested_service');
            var $productSelect = $modal.find('#intrested_product');
            var $branchSelect = $modal.find('#intrested_branch');
            
            if(partnerId != ''){
                if($('.popuploader').length) {
                    $('.popuploader').show();
                }
                $.ajax({
                    url: '{{URL::to('/getproduct')}}',
                    type: 'GET',
                    data: {cat_id: partnerId},
                    success: function(response){
                        if($('.popuploader').length) {
                            $('.popuploader').hide();
                        }
                        $productSelect.html(response);
                        reinitSelect2($productSelect, $modal);
                        $productSelect.val('').trigger('change');
                        
                        $branchSelect.html('<option value="">Please Select a Branch</option>');
                        reinitSelect2($branchSelect, $modal);
                        $branchSelect.val('').trigger('change');
                    },
                    error: function(){
                        if($('.popuploader').length) {
                            $('.popuploader').hide();
                        }
                        $productSelect.html('<option value="">Please Select a Product</option>');
                        reinitSelect2($productSelect, $modal);
                    }
                });
            } else {
                // Reset product and branch if partner is cleared
                $productSelect.html('<option value="">Please Select a Product</option>');
                reinitSelect2($productSelect, $modal);
                $branchSelect.html('<option value="">Please Select a Branch</option>');
                reinitSelect2($branchSelect, $modal);
            }
        });

        // Handle Product change - Load Branches
        $(document).delegate('.add_interested_service #intrested_product', 'change', function(){
            var productId = $(this).val();
            var $modal = $(this).closest('.add_interested_service');
            var $branchSelect = $modal.find('#intrested_branch');
            
            if(productId != ''){
                if($('.popuploader').length) {
                    $('.popuploader').show();
                }
                $.ajax({
                    url: '{{URL::to('/getbranch')}}',
                    type: 'GET',
                    data: {cat_id: productId},
                    success: function(response){
                        if($('.popuploader').length) {
                            $('.popuploader').hide();
                        }
                        $branchSelect.html(response);
                        reinitSelect2($branchSelect, $modal);
                        $branchSelect.val('').trigger('change');
                    },
                    error: function(){
                        if($('.popuploader').length) {
                            $('.popuploader').hide();
                        }
                        $branchSelect.html('<option value="">Please Select a Branch</option>');
                        reinitSelect2($branchSelect, $modal);
                    }
                });
            } else {
                // Reset branch if product is cleared
                $branchSelect.html('<option value="">Please Select a Branch</option>');
                reinitSelect2($branchSelect, $modal);
            }
        });
    });

    // Handle View button click for Interested Services
    $(document).delegate('.interest_service_view', 'click', function(){
        var v = $(this).attr('data-id');
        if($('.popuploader').length) {
            $('.popuploader').show();
        }
        // Show modal - try Bootstrap 5 first, fallback to jQuery
        var modalElement = document.getElementById('interest_service_view');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && modalElement) {
            var modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modal.show();
        } else if (typeof $.fn.modal !== 'undefined') {
            $('#interest_service_view').modal('show');
        }
        $.ajax({
            url: '{{URL::to('/getintrestedservice')}}',
            type: 'GET',
            data: {id: v},
            success: function(response){
                if($('.popuploader').length) {
                    $('.popuploader').hide();
                }
                $('.showinterestedservice').html(response);
            },
            error: function(){
                if($('.popuploader').length) {
                    $('.popuploader').hide();
                }
            }
        });
    });
</script>

@push('tinymce-scripts')
@include('partials.tinymce')
@endpush

<script>
    // ============================================================================
    // BULK UPLOAD FUNCTIONALITY FOR DOCUMENTS TAB
    // ============================================================================
    
    let bulkUploadFiles = [];
    let currentClientId = {{$fetchedData->id}};
    
    // Toggle bulk upload dropzone
    $(document).on('click', '.bulk-upload-toggle-btn', function() {
        const dropzoneContainer = $('.bulk-upload-dropzone-container');
        
        if (dropzoneContainer.is(':visible')) {
            dropzoneContainer.slideUp();
            $(this).html('<i class="fas fa-upload"></i> Bulk Upload');
            // Clear files
            bulkUploadFiles = [];
            dropzoneContainer.find('.bulk-upload-file-list').hide();
            dropzoneContainer.find('.file-count').text('0');
        } else {
            dropzoneContainer.slideDown();
            $(this).html('<i class="fas fa-times"></i> Close');
        }
    });
    
    // Click to browse files
    $(document).on('click', '.bulk-upload-dropzone', function(e) {
        if (!$(e.target).is('input')) {
            $('.bulk-upload-file-input').click();
        }
    });
    
    // File input change
    $(document).on('change', '.bulk-upload-file-input', function() {
        const files = this.files;
        if (files.length > 0) {
            handleBulkFilesSelected(files);
        }
    });
    
    // Drag and drop handlers
    $(document).on('dragover', '.bulk-upload-dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('drag_over');
    });
    
    $(document).on('dragleave', '.bulk-upload-dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag_over');
    });
    
    $(document).on('drop', '.bulk-upload-dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('drag_over');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files && files.length > 0) {
            handleBulkFilesSelected(files);
        }
    });
    
    // Handle files selected
    function handleBulkFilesSelected(files) {
        bulkUploadFiles = [];
        
        const invalidFiles = [];
        const maxSize = 50 * 1024 * 1024; // 50MB
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        
        Array.from(files).forEach(file => {
            if (file.size > maxSize) {
                invalidFiles.push(file.name + ' (exceeds 50MB)');
                return;
            }
            
            const ext = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                invalidFiles.push(file.name + ' (invalid file type)');
                return;
            }
            
            bulkUploadFiles.push(file);
        });
        
        if (invalidFiles.length > 0) {
            alert('The following files were skipped:\n' + invalidFiles.join('\n'));
        }
        
        if (bulkUploadFiles.length === 0) {
            alert('No valid files selected. Please select PDF, JPG, PNG, DOC, or DOCX files under 50MB.');
            return;
        }
        
        // Show file list
        $('.bulk-upload-file-list').show();
        $('.file-count').text(bulkUploadFiles.length);
        
        // Show mapping interface
        showBulkUploadMapping();
    }
    
    // Show mapping interface
    function showBulkUploadMapping() {
        if (bulkUploadFiles.length === 0) return;
        
        // Get existing checklists
        getExistingChecklists(function(checklists) {
            // Call backend to get auto-matches
            getAutoChecklistMatches(bulkUploadFiles, checklists, function(matches) {
                displayMappingInterface(bulkUploadFiles, checklists, matches);
            });
        });
    }
    
    // Get existing checklists from database
    function getExistingChecklists(callback) {
        $.ajax({
            url: '{{ route("clients.documents.getAutoChecklistMatches") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                clientid: currentClientId,
                files: [] // Empty array just to get checklists
            },
            success: function(response) {
                if (response.status && response.checklists) {
                    const checklists = response.checklists.map(name => ({ name: name }));
                    callback(checklists);
                } else {
                    // Fallback: get from table
                    const checklists = [];
                    const checklistNames = new Set();
                    
                    $('.alldocumnetlist tr').each(function() {
                        const checklistName = $(this).data('checklist-name');
                        if (checklistName && !checklistNames.has(checklistName)) {
                            checklistNames.add(checklistName);
                            checklists.push({ name: checklistName });
                        }
                    });
                    
                    callback(checklists);
                }
            },
            error: function() {
                // Fallback: get from table
                const checklists = [];
                const checklistNames = new Set();
                
                $('.alldocumnetlist tr').each(function() {
                    const checklistName = $(this).data('checklist-name');
                    if (checklistName && !checklistNames.has(checklistName)) {
                        checklistNames.add(checklistName);
                        checklists.push({ name: checklistName });
                    }
                });
                
                callback(checklists);
            }
        });
    }
    
    // Get auto-checklist matches from backend
    function getAutoChecklistMatches(files, checklists, callback) {
        const fileData = Array.from(files).map(file => ({
            name: file.name,
            size: file.size,
            type: file.type
        }));
        
        const checklistNames = checklists.map(c => c.name);
        
        $.ajax({
            url: '{{ route("clients.documents.getAutoChecklistMatches") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                clientid: currentClientId,
                files: fileData,
                checklists: checklistNames
            },
            success: function(response) {
                if (response.status) {
                    callback(response.matches || {});
                } else {
                    callback({});
                }
            },
            error: function() {
                callback({});
            }
        });
    }
    
    // Display mapping interface
    function displayMappingInterface(files, checklists, matches) {
        const modal = $('#bulk-upload-mapping-modal');
        const tableContainer = $('#bulk-upload-mapping-table');
        
        let html = '<div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">';
        html += '<table class="table table-bordered bulk-upload-table" style="width: 100%; min-width: 600px; margin-bottom: 0;">';
        html += '<thead><tr><th style="min-width: 150px;">File Name</th><th style="min-width: 200px;">Checklist Assignment</th><th style="min-width: 100px;">Status</th><th style="min-width: 80px;">Action</th></tr></thead>';
        html += '<tbody>';
        
        Array.from(files).forEach((file, index) => {
            const fileName = file.name;
            const fileSize = formatFileSize(file.size);
            const match = matches[fileName] || null;
            
            let selectedChecklist = '';
            let statusClass = 'manual';
            let statusText = 'Manual selection';
            
            if (match && match.checklist) {
                selectedChecklist = match.checklist;
                statusClass = match.confidence === 'high' ? 'auto-matched' : 'manual';
                statusText = match.confidence === 'high' ? 'Auto-matched' : 'Suggested';
            }
            
            html += '<tr class="bulk-upload-file-item">';
            html += '<td style="word-break: break-word;"><div class="file-info" style="display: flex; align-items: center; gap: 8px;"><i class="fas fa-file" style="color: #4a90e2; flex-shrink: 0;"></i><div style="min-width: 0; flex: 1;"><div class="file-name" style="word-break: break-word; overflow-wrap: break-word;">' + escapeHtml(fileName) + '</div><div class="file-size" style="font-size: 12px; color: #666;">' + fileSize + '</div></div></div></td>';
            html += '<td style="min-width: 200px;">';
            html += '<select class="form-control checklist-select" data-file-index="' + index + '" style="width: 100%;">';
            html += '<option value="">-- Select Checklist --</option>';
            html += '<option value="__NEW__">+ Create New Checklist</option>';
            checklists.forEach(checklist => {
                const selected = selectedChecklist === checklist.name ? 'selected' : '';
                html += '<option value="' + escapeHtml(checklist.name) + '" ' + selected + '>' + escapeHtml(checklist.name) + '</option>';
            });
            html += '</select>';
            html += '<input type="text" class="form-control mt-2 new-checklist-input" data-file-index="' + index + '" placeholder="Enter new checklist name" style="display: none; width: 100%;">';
            html += '</td>';
            html += '<td style="white-space: nowrap;"><span class="match-status ' + statusClass + '">' + statusText + '</span></td>';
            html += '<td style="white-space: nowrap;"><button type="button" class="btn btn-sm btn-outline-danger bulk-upload-remove-file" data-file-index="' + index + '">Remove</button></td>';
            html += '</tr>';
        });
        
        html += '</tbody></table>';
        html += '</div>';
        tableContainer.html(html);
        modal.show();
    }
    
    // Handle new checklist option
    $(document).on('change', '.checklist-select', function() {
        const fileIndex = $(this).data('file-index');
        const value = $(this).val();
        const newInput = $('.new-checklist-input[data-file-index="' + fileIndex + '"]');
        
        if (value === '__NEW__') {
            newInput.show();
            $(this).closest('tr').find('.match-status').removeClass('auto-matched manual').addClass('new-checklist').text('New checklist');
        } else {
            newInput.hide();
            if (value) {
                $(this).closest('tr').find('.match-status').removeClass('new-checklist').addClass('manual').text('Manual selection');
            }
        }
    });
    
    // Close modal
    $(document).on('click', '.close-mapping-modal, #cancel-bulk-upload', function() {
        $('#bulk-upload-mapping-modal').hide();
        $('#bulk-upload-progress').hide();
        $('#confirm-bulk-upload').prop('disabled', false);
    });

    // Remove a file from bulk upload
    $(document).on('click', '.bulk-upload-remove-file', function() {
        const index = parseInt($(this).data('file-index'), 10);
        if (Number.isNaN(index)) {
            return;
        }
        bulkUploadFiles.splice(index, 1);
        $('.file-count').text(bulkUploadFiles.length);
        if (bulkUploadFiles.length === 0) {
            $('#bulk-upload-mapping-modal').hide();
            $('.bulk-upload-file-list').hide();
            return;
        }
        showBulkUploadMapping();
    });
    
    // Confirm bulk upload
    $(document).on('click', '#confirm-bulk-upload', function() {
        const mappings = [];
        
        // Collect mappings
        bulkUploadFiles.forEach((file, index) => {
            const selectElement = $('.checklist-select[data-file-index="' + index + '"]');
            const checklist = selectElement.val();
            
            let mapping = null;
            
            if (checklist === '__NEW__') {
                const newChecklistName = $('.new-checklist-input[data-file-index="' + index + '"]').val();
                if (newChecklistName) {
                    mapping = { type: 'new', name: newChecklistName.trim() };
                }
            } else if (checklist) {
                mapping = { type: 'existing', name: checklist };
            }
            
            mappings.push(mapping);
        });
        
        // Validate all files have mappings
        const unmappedFiles = [];
        mappings.forEach((mapping, index) => {
            if (!mapping || !mapping.name) {
                unmappedFiles.push(bulkUploadFiles[index].name);
            }
        });
        
        if (unmappedFiles.length > 0) {
            alert('Please map all files to checklists:\n' + unmappedFiles.join('\n'));
            return;
        }
        
        // Upload files
        uploadBulkFiles(bulkUploadFiles, mappings);
    });
    
    // Upload bulk files
    function uploadBulkFiles(files, mappings) {
        const formData = new FormData();
        
        files.forEach((file, index) => {
            formData.append('files[]', file);
            formData.append('mappings[]', JSON.stringify(mappings[index]));
        });
        
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('clientid', currentClientId);
        formData.append('doctype', 'documents');
        formData.append('type', 'client');
        
        $('#bulk-upload-progress').show();
        $('#bulk-upload-progress-bar').css('width', '0%').text('0%');
        $('#confirm-bulk-upload').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("clients.documents.bulkUpload") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        $('#bulk-upload-progress-bar').css('width', percentComplete + '%').text(Math.round(percentComplete) + '%');
                    }
                });
                return xhr;
            },
            success: function(response) {
                $('#bulk-upload-progress').hide();
                $('#confirm-bulk-upload').prop('disabled', false);
                
                if (response.status) {
                    let message = response.message || 'Upload completed.';
                    if (response.errors && response.errors.length > 0) {
                        message += '\n\nSome files failed:\n' + response.errors.join('\n');
                    }
                    alert(message);
                    $('#bulk-upload-mapping-modal').hide();
                    $('.bulk-upload-dropzone-container').hide();
                    $('.bulk-upload-toggle-btn').html('<i class="fas fa-upload"></i> Bulk Upload');
                    bulkUploadFiles = [];
                    
                    // Reload the page to show new documents
                    location.reload();
                } else {
                    let errorMsg = 'Error: ' + (response.message || 'Upload failed.');
                    if (response.errors && response.errors.length > 0) {
                        errorMsg += '\n\nDetails:\n' + response.errors.join('\n');
                    }
                    alert(errorMsg);
                }
            },
            error: function(xhr) {
                $('#bulk-upload-progress').hide();
                $('#confirm-bulk-upload').prop('disabled', false);
                let errorMsg = 'Upload failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert(errorMsg);
            }
        });
    }
    
    // Helper functions
    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ============================================
    // Client Receipt Modal Handlers (FIX)
    // ============================================
    
    // Handle "Create Client Receipt" button click
    $(document).on('click', '.createclientreceipt', function() {
        // Reset form
        $('#create_client_receipt')[0].reset();
        
        // Set function_type to 'add' for new receipt
        $('#function_type').val('add');
        
        // Clear any existing rows except the first one
        $('.productitem tr.clonedrow:not(:first)').remove();
        
        // Clear the first row values
        $('.productitem tr.clonedrow:first').find('input, select').val('');
        $('.productitem tr.clonedrow:first').find('.unique_trans_no').val('');
        
        // Reset totals
        $('.total_deposit_amount_all_rows').html('');
        
        // Clear any error messages
        $('.custom-error-msg').html('');
        
        // Reset document upload section
        $('.docclientreceiptupload').val('');
        $('.selected-file-info').hide();
        $('.upload-receipt-doc-btn').html('<i class="fa fa-plus"></i> Add Document');
        $('.upload-receipt-doc-btn').removeClass('btn-success').addClass('btn-outline-primary');
        
        // Initialize flatpickr for date fields if not already initialized
        if (typeof flatpickr !== 'undefined') {
            $('.report_date_fields, .report_entry_date_fields').each(function() {
                if (!this._flatpickr) {
                    flatpickr(this, {
                        dateFormat: 'd/m/Y',
                        allowInput: true
                    });
                }
            });
        }
        
        // Open the modal
        $('#createclientreceiptmodal').modal('show');
    });
    
    // Handle "Edit Client Receipt" button click (from the pencil icon)
    $(document).on('click', '.updateclientreceipt', function() {
        var receipt_id = $(this).attr('data-id');
        
        // Set function_type to 'edit' for updating receipt
        $('#function_type').val('edit');
        
        // Clear any error messages
        $('.custom-error-msg').html('');
        
        // Show loader
        if ($('.popuploader').length) {
            $('.popuploader').show();
        }
        
        // Fetch receipt data from server
        $.ajax({
            url: '{{ url("/clients/getClientReceiptInfoById") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: receipt_id
            },
            success: function(response) {
                if ($('.popuploader').length) {
                    $('.popuploader').hide();
                }
                
                var obj = JSON.parse(response);
                
                if (obj.status) {
                    // Clear existing rows
                    $('.productitem').html('');
                    
                    // Populate form with fetched data
                    $.each(obj.requestData, function(index, data) {
                        var clonedRow = `
                            <tr class="clonedrow">
                                <td>
                                    <input data-valid="required" class="form-control report_date_fields" name="trans_date[]" type="text" value="${data.trans_date}" />
                                </td>
                                <td>
                                    <input data-valid="required" class="form-control report_entry_date_fields" name="entry_date[]" type="text" value="${data.entry_date}" />
                                </td>
                                <td>
                                    <input class="form-control unique_trans_no" type="text" value="${data.trans_no}" readonly/>
                                    <input class="unique_trans_no_hidden" name="trans_no[]" type="hidden" value="${data.trans_no}" />
                                    <input name="id[]" type="hidden" value="${data.id}" />
                                </td>
                                <td>
                                    <select data-valid="required" class="form-control" name="payment_method[]">
                                        <option value="">Select</option>
                                        <option value="Cash" ${data.payment_method == 'Cash' ? 'selected' : ''}>Cash</option>
                                        <option value="Bank transfer" ${data.payment_method == 'Bank transfer' ? 'selected' : ''}>Bank transfer</option>
                                        <option value="EFTPOS" ${data.payment_method == 'EFTPOS' ? 'selected' : ''}>EFTPOS</option>
                                    </select>
                                </td>
                                <td>
                                    <input data-valid="required" class="form-control" name="description[]" type="text" value="${data.description}" />
                                </td>
                                <td>
                                    <div class="currencyinput">
                                        <span>$</span>
                                        <input data-valid="required" class="form-control deposit_amount_per_row" name="deposit_amount[]" type="text" value="${data.deposit_amount}" />
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <a class="removeitems text-danger" href="javascript:;" title="Remove row">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                        $('.productitem').append(clonedRow);
                    });
                    
                    // Re-initialize flatpickr for date fields
                    if (typeof flatpickr !== 'undefined') {
                        $('.report_date_fields, .report_entry_date_fields').each(function() {
                            if (!this._flatpickr) {
                                flatpickr(this, {
                                    dateFormat: 'd/m/Y',
                                    allowInput: true
                                });
                            }
                        });
                    }
                    
                    // Calculate and display total
                    calculateReceiptTotal();
                    
                    // Open the modal
                    $('#createclientreceiptmodal').modal('show');
                } else {
                    alert('Error loading receipt data: ' + obj.message);
                }
            },
            error: function() {
                if ($('.popuploader').length) {
                    $('.popuploader').hide();
                }
                alert('Error loading receipt data. Please try again.');
            }
        });
    });
    
    // Calculate total deposit amount
    function calculateReceiptTotal() {
        var total = 0;
        $('.deposit_amount_per_row').each(function() {
            var amount = parseFloat($(this).val()) || 0;
            total += amount;
        });
        $('.total_deposit_amount_all_rows').html('$' + total.toFixed(2));
    }
    
    // Update total when deposit amount changes
    $(document).on('keyup change', '.deposit_amount_per_row', function() {
        calculateReceiptTotal();
    });
    
    // Add new line functionality (already in modal)
    $(document).on('click', '.openproductrinfo', function() {
        var clonedRow = $('.productitem tr.clonedrow:first').clone();
        clonedRow.find('input, select').val('');
        clonedRow.find('.unique_trans_no').val('');
        $('.productitem').append(clonedRow);
        
        // Re-initialize flatpickr for new row
        if (typeof flatpickr !== 'undefined') {
            clonedRow.find('.report_date_fields, .report_entry_date_fields').each(function() {
                flatpickr(this, {
                    dateFormat: 'd/m/Y',
                    allowInput: true
                });
            });
        }
        
        calculateReceiptTotal();
    });
    
    // Remove row functionality
    $(document).on('click', '.removeitems', function() {
        if ($('.productitem tr.clonedrow').length > 1) {
            $(this).closest('tr').remove();
            calculateReceiptTotal();
        } else {
            alert('At least one row is required.');
        }
    });
    
    // Document upload for receipt - Updated with file feedback
    $(document).on('click', '.upload-receipt-doc-btn', function() {
        $('.docclientreceiptupload').trigger('click');
    });
    
    // Handle file selection - show selected file name
    $(document).on('change', '.docclientreceiptupload', function() {
        var file = this.files[0];
        if (file) {
            var fileName = file.name;
            var fileSize = (file.size / 1024).toFixed(2); // Convert to KB
            
            // Show file info
            $('.file-name-display').text(fileName + ' (' + fileSize + ' KB)');
            $('.selected-file-info').slideDown();
            
            // Change button text to indicate file is attached
            $('.upload-receipt-doc-btn').html('<i class="fa fa-check"></i> Document Attached');
            $('.upload-receipt-doc-btn').removeClass('btn-outline-primary').addClass('btn-success');
        }
    });
    
    // Handle file removal
    $(document).on('click', '.remove-selected-file', function() {
        // Clear the file input
        $('.docclientreceiptupload').val('');
        
        // Hide file info
        $('.selected-file-info').slideUp();
        
        // Reset button
        $('.upload-receipt-doc-btn').html('<i class="fa fa-plus"></i> Add Document');
        $('.upload-receipt-doc-btn').removeClass('btn-success').addClass('btn-outline-primary');
    });
    
</script>

@endsection
