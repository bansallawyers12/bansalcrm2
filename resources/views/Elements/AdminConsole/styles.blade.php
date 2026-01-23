<style>
/* AdminConsole Standard Design - Remove floating card appearance for seamless page design */
.main-content {
	padding: 0 !important;
	margin-left: 250px !important;
	margin-top: 0 !important;
	padding-top: 0 !important;
	width: calc(100% - 250px) !important;
}

.main-content .section {
	padding: 0 !important;
	margin: 0 !important;
	margin-top: 0 !important;
	padding-top: 0 !important;
	background: #fff;
	min-height: calc(100vh - 80px);
}

.main-content .section-body {
	padding: 0 !important;
	margin: 0 !important;
	margin-top: 0 !important;
	padding-top: 0 !important;
	line-height: 0 !important;
	font-size: 0 !important;
}

.main-content .section-body > * {
	line-height: normal;
	font-size: 14px;
}

.main-content .section-body .row {
	margin: 0 !important;
	padding: 0 !important;
}

.main-content .section-body .col-12 {
	padding: 0 !important;
	margin: 0 !important;
}

.main-content .section-body .card {
	border: none;
	border-radius: 0;
	box-shadow: none;
	margin: 0;
	background: transparent;
}

.main-content .section-body .card-header {
	background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
	border-bottom: 2px solid #cbd5e1;
	padding: 24px 30px;
	margin: 0;
	display: flex;
	justify-content: space-between;
	align-items: center;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.main-content .section-body .card-header h4 {
	margin: 0;
	color: #1a202c;
	font-size: 26px;
	font-weight: 800;
	letter-spacing: -0.5px;
	text-transform: uppercase;
	position: relative;
	padding-left: 15px;
}

.main-content .section-body .card-header h4::before {
	content: '';
	position: absolute;
	left: 0;
	top: 50%;
	transform: translateY(-50%);
	width: 4px;
	height: 28px;
	background: linear-gradient(135deg, #6777ef 0%, #764ba2 100%);
	border-radius: 2px;
}

.main-content .section-body .card-header .card-header-action {
	display: flex;
	align-items: center;
}

.main-content .section-body .card-header .btn-primary {
	padding: 10px 20px;
	font-weight: 600;
	font-size: 14px;
	border-radius: 6px;
	box-shadow: 0 2px 4px rgba(103, 119, 239, 0.2);
	transition: all 0.3s ease;
}

.main-content .section-body .card-header .btn-primary:hover {
	transform: translateY(-1px);
	box-shadow: 0 4px 8px rgba(103, 119, 239, 0.3);
}

.main-content .section-body .card-body {
	padding: 20px 30px;
	background: #fff;
}

.main-content .section-body .table-responsive {
	margin: 0;
}

.main-content .section-body .table {
	margin-bottom: 0;
}

.server-error,
.custom-error-msg {
	padding: 0 !important;
	margin: 0 !important;
	margin-top: 0 !important;
	margin-bottom: 0 !important;
	min-height: 0 !important;
	height: auto !important;
	line-height: 0 !important;
}

/* Hide empty error divs */
.server-error:empty,
.custom-error-msg:empty {
	display: none !important;
	height: 0 !important;
	min-height: 0 !important;
	padding: 0 !important;
	margin: 0 !important;
}

/* Remove any gap before the card header */
.main-content .section-body > .server-error:first-child,
.main-content .section-body > .custom-error-msg:first-child {
	margin-top: 0 !important;
	padding-top: 0 !important;
}

.main-content .section-body > .row:first-child {
	margin-top: 0 !important;
	padding-top: 0 !important;
}

.main-content .section-body > .row:first-child .card {
	margin-top: 0 !important;
	padding-top: 0 !important;
}

.main-content .section-body > .row:first-child .card .card-header {
	margin-top: 0 !important;
	padding-top: 0 !important;
}

/* Ensure no spacing before first element */
.main-content .section-body > *:first-child {
	margin-top: 0 !important;
	padding-top: 0 !important;
}

/* Remove any gap - ensure content starts at top */
.main-content .section-body {
	display: flex;
	flex-direction: column;
}

.main-content .section-body .row {
	flex: 0 0 auto;
}

/* Make sure the card header is the first visible element with no gap above */
.main-content .section-body .row .card .card-header {
	margin-top: 0 !important;
	padding-top: 24px !important;
}

/* Remove any whitespace/gap from line breaks */
.main-content .section-body {
	display: block;
}

.main-content .section-body .server-error:empty + .custom-error-msg:empty + .row {
	margin-top: 0 !important;
}
</style>
