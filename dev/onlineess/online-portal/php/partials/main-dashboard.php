<?php 
	
	session_start();
	if(!isset($_SESSION['user_id']))
	{	
		session_destroy();
		header('location: ../../login.php');
		exit();
	}

	//echo var_dump($_SESSION);
?>

<!DOCTYPE html>
<html style="overflow: hidden;">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="../../image/icons/fcpc_logo.ico"/>

		<meta http-equiv='cache-control' content='no-cache'>
		<meta http-equiv='expires' content='0'>
		<meta http-equiv='pragma' content='no-cache'>
		<title>ESS PORTAL DASHBOARD</title>

		<style type="text/css">
			/* p, label
			{
				color: royalblue;
				font-weight: bold;
				display: block;
				float: left;
			} */

			#body-pd {

					margin-bottom: -50px;

			}
			.footer {
				position: fixed;
				margin-top: 50px;
				left: 0;
				bottom: 0;
				width: 100%;
				text-align: center;
				background-color: white;
			}

			.headline {
				margin: auto; 
				padding: 10px; 
				height: auto; 
				border-radius: 5px; 
				/* background-color: rgb(65, 105, 225);	*/
				background-color: #677DB7;
				color: #00000;
			}

			.header_div{
				margin: auto; 
				padding: 10px; 
				height: auto; 
				border-radius: 5px; 
				/* background-color: rgb(65, 105, 225);	*/
				background-color: #F5E614;
				color: #00000;
			}

			.notification {
				display: inline-block;
			}

			.notification:hover {
				background: #0a58ca;
			}

			.notification .badge {
				position: absolute;
				top: -10px;
				left: 10px;
				padding: 5px 10px;
				border-radius: 50%;
				background-color: red;
				color: white;
			}
		</style>

		<style type="text/css">

			table.paleBlueRows {
				font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
				border: 2px solid #444444;
				width: 100%;
				height: 200px;
				text-align: center;
				border-collapse: collapse;
			}
			table.paleBlueRows td, table.paleBlueRows th {
				/* border: 2px solid #444444; */
				padding: 5px 5px;
				text-align: center;

			}
			table.paleBlueRows tbody td {
				font-size: 13px;
			}
			table.paleBlueRows tr:nth-child(even) {
				background: #D0E4F5;
			}
			table.paleBlueRows thead {
				background: #0B6FA4;
				/* border-bottom: 1px solid #FFFFFF; */
			}
			table.paleBlueRows thead th {
				font-size: 17px;
				font-weight: bold;
				color: #FFFFFF;
				text-align: center;
				/* border-left: 1px solid #FFFFFF; */
			}
			table.paleBlueRows thead th:first-child {
				border-left: none;
			}

			table.paleBlueRows tfoot {
				font-size: 14px;
				font-weight: bold;
				color: #FFFFFF;
				background: #0E8BCD;
				border-top: 1px solid #FFFFFF;
			}
			table.paleBlueRows tfoot td {
				font-size: 14px;
			}

			.ellipsis {
				position: relative;
			}

			.ellipsis:before {
				content: ' ';
				visibility: hidden;
			}
			
			.ellipsis span {
				position: absolute;
				left: 10px;
				right: 10px;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}

			input[type=text],input[type=date],input[type=number],input[type=email]{
				padding: 10px;
				/* border-bottom: 2px solid royalblue; */
			}

			hr{
				border-top: 8px solid #adb5bd;
				border-radius: 5px;
				opacity: 75%;
			}

			hr.dashed{
				border-top: 8px dashed #adb5bd;
				opacity: 75%;
			}

			.card {
				position: relative;
				display: -ms-flexbox;
				display: flex;
				-ms-flex-direction: column;
				flex-direction: column;
				min-width: 0;
				word-wrap: break-word;
				background-color: #fff;
				background-clip: border-box;
				border: 0 solid rgba(0, 0, 0, 0.125);
				border-radius: 0.25rem;
			}

			.card-primary:not(.card-outline)>.card-header {
				background-color: #007bff;
			}

			.card-primary:not(.card-outline)>.card-header,
			.card-primary:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-primary:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-primary.card-outline {
				border-top: 3px solid #007bff;
			}

			.card-primary.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-primary.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #007bff;
			}

			.bg-primary .btn-tool,
			.bg-gradient-primary .btn-tool,
			.card-primary:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-primary .btn-tool:hover,
			.bg-gradient-primary .btn-tool:hover,
			.card-primary:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-primary .bootstrap-datetimepicker-widget .table td,
			.card.bg-primary .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-primary .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-primary .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-primary .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-primary .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-primary .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #0067d6;
				color: #fff;
			}

			.card.bg-primary .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-primary .bootstrap-datetimepicker-widget table td.active,
			.card.bg-primary .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-primary .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #3395ff;
				color: #fff;
			}

			.card-secondary:not(.card-outline)>.card-header {
				background-color: #6c757d;
			}

			.card-secondary:not(.card-outline)>.card-header,
			.card-secondary:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-secondary:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-secondary.card-outline {
				border-top: 3px solid #6c757d;
			}

			.card-secondary.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-secondary.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #6c757d;
			}

			.bg-secondary .btn-tool,
			.bg-gradient-secondary .btn-tool,
			.card-secondary:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-secondary .btn-tool:hover,
			.bg-gradient-secondary .btn-tool:hover,
			.card-secondary:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-secondary .bootstrap-datetimepicker-widget .table td,
			.card.bg-secondary .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-secondary .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-secondary .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-secondary .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-secondary .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-secondary .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #596167;
				color: #fff;
			}

			.card.bg-secondary .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-secondary .bootstrap-datetimepicker-widget table td.active,
			.card.bg-secondary .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-secondary .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #868e96;
				color: #fff;
			}

			.card-success:not(.card-outline)>.card-header {
				background-color: #28a745;
			}

			.card-success:not(.card-outline)>.card-header,
			.card-success:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-success:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-success.card-outline {
				border-top: 3px solid #28a745;
			}

			.card-success.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-success.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #28a745;
			}

			.bg-success .btn-tool,
			.bg-gradient-success .btn-tool,
			.card-success:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-success .btn-tool:hover,
			.bg-gradient-success .btn-tool:hover,
			.card-success:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-success .bootstrap-datetimepicker-widget .table td,
			.card.bg-success .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-success .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-success .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-success .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-success .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-success .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #208637;
				color: #fff;
			}

			.card.bg-success .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-success .bootstrap-datetimepicker-widget table td.active,
			.card.bg-success .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-success .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #34ce57;
				color: #fff;
			}

			.card-info:not(.card-outline)>.card-header {
				background-color: #17a2b8;
			}

			.card-info:not(.card-outline)>.card-header,
			.card-info:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-info:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-info.card-outline {
				border-top: 3px solid #17a2b8;
			}

			.card-info.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-info.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #17a2b8;
			}

			.bg-info .btn-tool,
			.bg-gradient-info .btn-tool,
			.card-info:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-info .btn-tool:hover,
			.bg-gradient-info .btn-tool:hover,
			.card-info:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-info .bootstrap-datetimepicker-widget .table td,
			.card.bg-info .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-info .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-info .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-info .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-info .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-info .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #128294;
				color: #fff;
			}

			.card.bg-info .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-info .bootstrap-datetimepicker-widget table td.active,
			.card.bg-info .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-info .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #1fc8e3;
				color: #fff;
			}

			.card-warning:not(.card-outline)>.card-header {
				background-color: #ffc107;
			}

			.card-warning:not(.card-outline)>.card-header,
			.card-warning:not(.card-outline)>.card-header a {
				color: #1f2d3d;
			}

			.card-warning:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-warning.card-outline {
				border-top: 3px solid #ffc107;
			}

			.card-warning.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-warning.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #ffc107;
			}

			.bg-warning .btn-tool,
			.bg-gradient-warning .btn-tool,
			.card-warning:not(.card-outline) .btn-tool {
				color: rgba(31, 45, 61, 0.8);
			}

			.bg-warning .btn-tool:hover,
			.bg-gradient-warning .btn-tool:hover,
			.card-warning:not(.card-outline) .btn-tool:hover {
				color: #1f2d3d;
			}

			.card.bg-warning .bootstrap-datetimepicker-widget .table td,
			.card.bg-warning .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-warning .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-warning .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-warning .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-warning .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-warning .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #dda600;
				color: #1f2d3d;
			}

			.card.bg-warning .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #1f2d3d;
			}

			.card.bg-warning .bootstrap-datetimepicker-widget table td.active,
			.card.bg-warning .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-warning .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #ffce3a;
				color: #1f2d3d;
			}

			.card-danger:not(.card-outline)>.card-header {
				background-color: #dc3545;
			}

			.card-danger:not(.card-outline)>.card-header,
			.card-danger:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-danger:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-danger.card-outline {
				border-top: 3px solid #dc3545;
			}

			.card-danger.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-danger.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #dc3545;
			}

			.bg-danger .btn-tool,
			.bg-gradient-danger .btn-tool,
			.card-danger:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-danger .btn-tool:hover,
			.bg-gradient-danger .btn-tool:hover,
			.card-danger:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-danger .bootstrap-datetimepicker-widget .table td,
			.card.bg-danger .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-danger .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-danger .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-danger .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-danger .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-danger .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #c62232;
				color: #fff;
			}

			.card.bg-danger .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-danger .bootstrap-datetimepicker-widget table td.active,
			.card.bg-danger .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-danger .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #e4606d;
				color: #fff;
			}

			.card-light:not(.card-outline)>.card-header {
				background-color: #f8f9fa;
			}

			.card-light:not(.card-outline)>.card-header,
			.card-light:not(.card-outline)>.card-header a {
				color: #1f2d3d;
			}

			.card-light:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-light.card-outline {
				border-top: 3px solid #f8f9fa;
			}

			.card-light.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-light.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #f8f9fa;
			}

			.bg-light .btn-tool,
			.bg-gradient-light .btn-tool,
			.card-light:not(.card-outline) .btn-tool {
				color: rgba(31, 45, 61, 0.8);
			}

			.bg-light .btn-tool:hover,
			.bg-gradient-light .btn-tool:hover,
			.card-light:not(.card-outline) .btn-tool:hover {
				color: #1f2d3d;
			}

			.card.bg-light .bootstrap-datetimepicker-widget .table td,
			.card.bg-light .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-light .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-light .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-light .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-light .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-light .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #e0e5e9;
				color: #1f2d3d;
			}

			.card.bg-light .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #1f2d3d;
			}

			.card.bg-light .bootstrap-datetimepicker-widget table td.active,
			.card.bg-light .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-light .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: white;
				color: #1f2d3d;
			}

			.card-dark:not(.card-outline)>.card-header {
				background-color: #343a40;
			}

			.card-dark:not(.card-outline)>.card-header,
			.card-dark:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-dark:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-dark.card-outline {
				border-top: 3px solid #343a40;
			}

			.card-dark.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-dark.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #343a40;
			}

			.bg-dark .btn-tool,
			.bg-gradient-dark .btn-tool,
			.card-dark:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-dark .btn-tool:hover,
			.bg-gradient-dark .btn-tool:hover,
			.card-dark:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-dark .bootstrap-datetimepicker-widget .table td,
			.card.bg-dark .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-dark .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-dark .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-dark .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-dark .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-dark .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #222629;
				color: #fff;
			}

			.card.bg-dark .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-dark .bootstrap-datetimepicker-widget table td.active,
			.card.bg-dark .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-dark .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #4b545c;
				color: #fff;
			}

			.card-lightblue:not(.card-outline)>.card-header {
				background-color: #3c8dbc;
			}

			.card-lightblue:not(.card-outline)>.card-header,
			.card-lightblue:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-lightblue:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-lightblue.card-outline {
				border-top: 3px solid #3c8dbc;
			}

			.card-lightblue.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-lightblue.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #3c8dbc;
			}

			.bg-lightblue .btn-tool,
			.bg-gradient-lightblue .btn-tool,
			.card-lightblue:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-lightblue .btn-tool:hover,
			.bg-gradient-lightblue .btn-tool:hover,
			.card-lightblue:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-lightblue .bootstrap-datetimepicker-widget .table td,
			.card.bg-lightblue .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-lightblue .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #32769d;
				color: #fff;
			}

			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.active,
			.card.bg-lightblue .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-lightblue .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #5fa4cc;
				color: #fff;
			}

			.card-navy:not(.card-outline)>.card-header {
				background-color: #001f3f;
			}

			.card-navy:not(.card-outline)>.card-header,
			.card-navy:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-navy:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-navy.card-outline {
				border-top: 3px solid #001f3f;
			}

			.card-navy.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-navy.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #001f3f;
			}

			.bg-navy .btn-tool,
			.bg-gradient-navy .btn-tool,
			.card-navy:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-navy .btn-tool:hover,
			.bg-gradient-navy .btn-tool:hover,
			.card-navy:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-navy .bootstrap-datetimepicker-widget .table td,
			.card.bg-navy .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-navy .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-navy .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-navy .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-navy .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-navy .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #000b16;
				color: #fff;
			}

			.card.bg-navy .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-navy .bootstrap-datetimepicker-widget table td.active,
			.card.bg-navy .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-navy .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #003872;
				color: #fff;
			}

			.card-olive:not(.card-outline)>.card-header {
				background-color: #3d9970;
			}

			.card-olive:not(.card-outline)>.card-header,
			.card-olive:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-olive:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-olive.card-outline {
				border-top: 3px solid #3d9970;
			}

			.card-olive.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-olive.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #3d9970;
			}

			.bg-olive .btn-tool,
			.bg-gradient-olive .btn-tool,
			.card-olive:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-olive .btn-tool:hover,
			.bg-gradient-olive .btn-tool:hover,
			.card-olive:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-olive .bootstrap-datetimepicker-widget .table td,
			.card.bg-olive .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-olive .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-olive .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-olive .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-olive .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-olive .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #317c5b;
				color: #fff;
			}

			.card.bg-olive .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-olive .bootstrap-datetimepicker-widget table td.active,
			.card.bg-olive .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-olive .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #50b98a;
				color: #fff;
			}

			.card-lime:not(.card-outline)>.card-header {
				background-color: #01ff70;
			}

			.card-lime:not(.card-outline)>.card-header,
			.card-lime:not(.card-outline)>.card-header a {
				color: #1f2d3d;
			}

			.card-lime:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-lime.card-outline {
				border-top: 3px solid #01ff70;
			}

			.card-lime.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-lime.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #01ff70;
			}

			.bg-lime .btn-tool,
			.bg-gradient-lime .btn-tool,
			.card-lime:not(.card-outline) .btn-tool {
				color: rgba(31, 45, 61, 0.8);
			}

			.bg-lime .btn-tool:hover,
			.bg-gradient-lime .btn-tool:hover,
			.card-lime:not(.card-outline) .btn-tool:hover {
				color: #1f2d3d;
			}

			.card.bg-lime .bootstrap-datetimepicker-widget .table td,
			.card.bg-lime .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-lime .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-lime .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-lime .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-lime .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-lime .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #00d75e;
				color: #1f2d3d;
			}

			.card.bg-lime .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #1f2d3d;
			}

			.card.bg-lime .bootstrap-datetimepicker-widget table td.active,
			.card.bg-lime .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-lime .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #34ff8d;
				color: #1f2d3d;
			}

			.card-fuchsia:not(.card-outline)>.card-header {
				background-color: #f012be;
			}

			.card-fuchsia:not(.card-outline)>.card-header,
			.card-fuchsia:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-fuchsia:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-fuchsia.card-outline {
				border-top: 3px solid #f012be;
			}

			.card-fuchsia.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-fuchsia.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #f012be;
			}

			.bg-fuchsia .btn-tool,
			.bg-gradient-fuchsia .btn-tool,
			.card-fuchsia:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-fuchsia .btn-tool:hover,
			.bg-gradient-fuchsia .btn-tool:hover,
			.card-fuchsia:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-fuchsia .bootstrap-datetimepicker-widget .table td,
			.card.bg-fuchsia .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-fuchsia .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #cc0da1;
				color: #fff;
			}

			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.active,
			.card.bg-fuchsia .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-fuchsia .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #f342cb;
				color: #fff;
			}

			.card-maroon:not(.card-outline)>.card-header {
				background-color: #d81b60;
			}

			.card-maroon:not(.card-outline)>.card-header,
			.card-maroon:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-maroon:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-maroon.card-outline {
				border-top: 3px solid #d81b60;
			}

			.card-maroon.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-maroon.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #d81b60;
			}

			.bg-maroon .btn-tool,
			.bg-gradient-maroon .btn-tool,
			.card-maroon:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-maroon .btn-tool:hover,
			.bg-gradient-maroon .btn-tool:hover,
			.card-maroon:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-maroon .bootstrap-datetimepicker-widget .table td,
			.card.bg-maroon .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-maroon .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-maroon .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-maroon .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-maroon .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-maroon .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #b41650;
				color: #fff;
			}

			.card.bg-maroon .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-maroon .bootstrap-datetimepicker-widget table td.active,
			.card.bg-maroon .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-maroon .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #e73f7c;
				color: #fff;
			}

			.card-blue:not(.card-outline)>.card-header {
				background-color: #007bff;
			}

			.card-blue:not(.card-outline)>.card-header,
			.card-blue:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-blue:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-blue.card-outline {
				border-top: 3px solid #007bff;
			}

			.card-blue.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-blue.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #007bff;
			}

			.bg-blue .btn-tool,
			.bg-gradient-blue .btn-tool,
			.card-blue:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-blue .btn-tool:hover,
			.bg-gradient-blue .btn-tool:hover,
			.card-blue:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-blue .bootstrap-datetimepicker-widget .table td,
			.card.bg-blue .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-blue .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-blue .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-blue .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-blue .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-blue .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #0067d6;
				color: #fff;
			}

			.card.bg-blue .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-blue .bootstrap-datetimepicker-widget table td.active,
			.card.bg-blue .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-blue .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #3395ff;
				color: #fff;
			}

			.card-indigo:not(.card-outline)>.card-header {
				background-color: #6610f2;
			}

			.card-indigo:not(.card-outline)>.card-header,
			.card-indigo:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-indigo:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-indigo.card-outline {
				border-top: 3px solid #6610f2;
			}

			.card-indigo.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-indigo.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #6610f2;
			}

			.bg-indigo .btn-tool,
			.bg-gradient-indigo .btn-tool,
			.card-indigo:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-indigo .btn-tool:hover,
			.bg-gradient-indigo .btn-tool:hover,
			.card-indigo:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-indigo .bootstrap-datetimepicker-widget .table td,
			.card.bg-indigo .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-indigo .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-indigo .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-indigo .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-indigo .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-indigo .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #550bce;
				color: #fff;
			}

			.card.bg-indigo .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-indigo .bootstrap-datetimepicker-widget table td.active,
			.card.bg-indigo .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-indigo .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #8540f5;
				color: #fff;
			}

			.card-purple:not(.card-outline)>.card-header {
				background-color: #6f42c1;
			}

			.card-purple:not(.card-outline)>.card-header,
			.card-purple:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-purple:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-purple.card-outline {
				border-top: 3px solid #6f42c1;
			}

			.card-purple.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-purple.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #6f42c1;
			}

			.bg-purple .btn-tool,
			.bg-gradient-purple .btn-tool,
			.card-purple:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-purple .btn-tool:hover,
			.bg-gradient-purple .btn-tool:hover,
			.card-purple:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-purple .bootstrap-datetimepicker-widget .table td,
			.card.bg-purple .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-purple .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-purple .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-purple .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-purple .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-purple .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #5d36a4;
				color: #fff;
			}

			.card.bg-purple .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-purple .bootstrap-datetimepicker-widget table td.active,
			.card.bg-purple .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-purple .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #8c68ce;
				color: #fff;
			}

			.card-pink:not(.card-outline)>.card-header {
				background-color: #e83e8c;
			}

			.card-pink:not(.card-outline)>.card-header,
			.card-pink:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-pink:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-pink.card-outline {
				border-top: 3px solid #e83e8c;
			}

			.card-pink.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-pink.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #e83e8c;
			}

			.bg-pink .btn-tool,
			.bg-gradient-pink .btn-tool,
			.card-pink:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-pink .btn-tool:hover,
			.bg-gradient-pink .btn-tool:hover,
			.card-pink:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-pink .bootstrap-datetimepicker-widget .table td,
			.card.bg-pink .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-pink .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-pink .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-pink .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-pink .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-pink .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #e21b76;
				color: #fff;
			}

			.card.bg-pink .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-pink .bootstrap-datetimepicker-widget table td.active,
			.card.bg-pink .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-pink .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #ed6ca7;
				color: #fff;
			}

			.card-red:not(.card-outline)>.card-header {
				background-color: #dc3545;
			}

			.card-red:not(.card-outline)>.card-header,
			.card-red:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-red:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-red.card-outline {
				border-top: 3px solid #dc3545;
			}

			.card-red.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-red.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #dc3545;
			}

			.bg-red .btn-tool,
			.bg-gradient-red .btn-tool,
			.card-red:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-red .btn-tool:hover,
			.bg-gradient-red .btn-tool:hover,
			.card-red:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-red .bootstrap-datetimepicker-widget .table td,
			.card.bg-red .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-red .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-red .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-red .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-red .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-red .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #c62232;
				color: #fff;
			}

			.card.bg-red .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-red .bootstrap-datetimepicker-widget table td.active,
			.card.bg-red .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-red .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #e4606d;
				color: #fff;
			}

			.card-orange:not(.card-outline)>.card-header {
				background-color: #fd7e14;
			}

			.card-orange:not(.card-outline)>.card-header,
			.card-orange:not(.card-outline)>.card-header a {
				color: #1f2d3d;
			}

			.card-orange:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-orange.card-outline {
				border-top: 3px solid #fd7e14;
			}

			.card-orange.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-orange.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #fd7e14;
			}

			.bg-orange .btn-tool,
			.bg-gradient-orange .btn-tool,
			.card-orange:not(.card-outline) .btn-tool {
				color: rgba(31, 45, 61, 0.8);
			}

			.bg-orange .btn-tool:hover,
			.bg-gradient-orange .btn-tool:hover,
			.card-orange:not(.card-outline) .btn-tool:hover {
				color: #1f2d3d;
			}

			.card.bg-orange .bootstrap-datetimepicker-widget .table td,
			.card.bg-orange .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-orange .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-orange .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-orange .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-orange .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-orange .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #e66a02;
				color: #1f2d3d;
			}

			.card.bg-orange .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #1f2d3d;
			}

			.card.bg-orange .bootstrap-datetimepicker-widget table td.active,
			.card.bg-orange .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-orange .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #fd9a47;
				color: #1f2d3d;
			}

			.card-yellow:not(.card-outline)>.card-header {
				background-color: #ffc107;
			}

			.card-yellow:not(.card-outline)>.card-header,
			.card-yellow:not(.card-outline)>.card-header a {
				color: #1f2d3d;
			}

			.card-yellow:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-yellow.card-outline {
				border-top: 3px solid #ffc107;
			}

			.card-yellow.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-yellow.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #ffc107;
			}

			.bg-yellow .btn-tool,
			.bg-gradient-yellow .btn-tool,
			.card-yellow:not(.card-outline) .btn-tool {
				color: rgba(31, 45, 61, 0.8);
			}

			.bg-yellow .btn-tool:hover,
			.bg-gradient-yellow .btn-tool:hover,
			.card-yellow:not(.card-outline) .btn-tool:hover {
				color: #1f2d3d;
			}

			.card.bg-yellow .bootstrap-datetimepicker-widget .table td,
			.card.bg-yellow .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-yellow .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-yellow .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-yellow .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-yellow .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-yellow .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #dda600;
				color: #1f2d3d;
			}

			.card.bg-yellow .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #1f2d3d;
			}

			.card.bg-yellow .bootstrap-datetimepicker-widget table td.active,
			.card.bg-yellow .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-yellow .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #ffce3a;
				color: #1f2d3d;
			}

			.card-green:not(.card-outline)>.card-header {
				background-color: #28a745;
			}

			.card-green:not(.card-outline)>.card-header,
			.card-green:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-green:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-green.card-outline {
				border-top: 3px solid #28a745;
			}

			.card-green.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-green.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #28a745;
			}

			.bg-green .btn-tool,
			.bg-gradient-green .btn-tool,
			.card-green:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-green .btn-tool:hover,
			.bg-gradient-green .btn-tool:hover,
			.card-green:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-green .bootstrap-datetimepicker-widget .table td,
			.card.bg-green .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-green .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-green .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-green .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-green .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-green .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #208637;
				color: #fff;
			}

			.card.bg-green .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-green .bootstrap-datetimepicker-widget table td.active,
			.card.bg-green .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-green .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #34ce57;
				color: #fff;
			}

			.card-teal:not(.card-outline)>.card-header {
				background-color: #20c997;
			}

			.card-teal:not(.card-outline)>.card-header,
			.card-teal:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-teal:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-teal.card-outline {
				border-top: 3px solid #20c997;
			}

			.card-teal.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-teal.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #20c997;
			}

			.bg-teal .btn-tool,
			.bg-gradient-teal .btn-tool,
			.card-teal:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-teal .btn-tool:hover,
			.bg-gradient-teal .btn-tool:hover,
			.card-teal:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-teal .bootstrap-datetimepicker-widget .table td,
			.card.bg-teal .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-teal .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-teal .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-teal .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-teal .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-teal .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #1aa67d;
				color: #fff;
			}

			.card.bg-teal .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-teal .bootstrap-datetimepicker-widget table td.active,
			.card.bg-teal .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-teal .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #3ce0af;
				color: #fff;
			}

			.card-cyan:not(.card-outline)>.card-header {
				background-color: #17a2b8;
			}

			.card-cyan:not(.card-outline)>.card-header,
			.card-cyan:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-cyan:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-cyan.card-outline {
				border-top: 3px solid #17a2b8;
			}

			.card-cyan.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-cyan.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #17a2b8;
			}

			.bg-cyan .btn-tool,
			.bg-gradient-cyan .btn-tool,
			.card-cyan:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-cyan .btn-tool:hover,
			.bg-gradient-cyan .btn-tool:hover,
			.card-cyan:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-cyan .bootstrap-datetimepicker-widget .table td,
			.card.bg-cyan .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-cyan .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-cyan .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-cyan .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-cyan .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-cyan .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #128294;
				color: #fff;
			}

			.card.bg-cyan .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-cyan .bootstrap-datetimepicker-widget table td.active,
			.card.bg-cyan .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-cyan .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #1fc8e3;
				color: #fff;
			}

			.card-white:not(.card-outline)>.card-header {
				background-color: #fff;
			}

			.card-white:not(.card-outline)>.card-header,
			.card-white:not(.card-outline)>.card-header a {
				color: #1f2d3d;
			}

			.card-white:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-white.card-outline {
				border-top: 3px solid #fff;
			}

			.card-white.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-white.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #fff;
			}

			.bg-white .btn-tool,
			.bg-gradient-white .btn-tool,
			.card-white:not(.card-outline) .btn-tool {
				color: rgba(31, 45, 61, 0.8);
			}

			.bg-white .btn-tool:hover,
			.bg-gradient-white .btn-tool:hover,
			.card-white:not(.card-outline) .btn-tool:hover {
				color: #1f2d3d;
			}

			.card.bg-white .bootstrap-datetimepicker-widget .table td,
			.card.bg-white .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-white .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-white .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-white .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-white .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-white .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #ebebeb;
				color: #1f2d3d;
			}

			.card.bg-white .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #1f2d3d;
			}

			.card.bg-white .bootstrap-datetimepicker-widget table td.active,
			.card.bg-white .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-white .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: white;
				color: #1f2d3d;
			}

			.card-gray:not(.card-outline)>.card-header {
				background-color: #6c757d;
			}

			.card-gray:not(.card-outline)>.card-header,
			.card-gray:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-gray:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-gray.card-outline {
				border-top: 3px solid #6c757d;
			}

			.card-gray.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-gray.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #6c757d;
			}

			.bg-gray .btn-tool,
			.bg-gradient-gray .btn-tool,
			.card-gray:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-gray .btn-tool:hover,
			.bg-gradient-gray .btn-tool:hover,
			.card-gray:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-gray .bootstrap-datetimepicker-widget .table td,
			.card.bg-gray .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-gray .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gray .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gray .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gray .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gray .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #596167;
				color: #fff;
			}

			.card.bg-gray .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-gray .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gray .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-gray .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #868e96;
				color: #fff;
			}

			.card-gray-dark:not(.card-outline)>.card-header {
				background-color: #343a40;
			}

			.card-gray-dark:not(.card-outline)>.card-header,
			.card-gray-dark:not(.card-outline)>.card-header a {
				color: #fff;
			}

			.card-gray-dark:not(.card-outline)>.card-header a.active {
				color: #1f2d3d;
			}

			.card-gray-dark.card-outline {
				border-top: 3px solid #343a40;
			}

			.card-gray-dark.card-outline-tabs>.card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card-gray-dark.card-outline-tabs>.card-header a.active {
				border-top: 3px solid #343a40;
			}

			.bg-gray-dark .btn-tool,
			.bg-gradient-gray-dark .btn-tool,
			.card-gray-dark:not(.card-outline) .btn-tool {
				color: rgba(255, 255, 255, 0.8);
			}

			.bg-gray-dark .btn-tool:hover,
			.bg-gradient-gray-dark .btn-tool:hover,
			.card-gray-dark:not(.card-outline) .btn-tool:hover {
				color: #fff;
			}

			.card.bg-gray-dark .bootstrap-datetimepicker-widget .table td,
			.card.bg-gray-dark .bootstrap-datetimepicker-widget .table th,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget .table td,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget .table th {
				border: none;
			}

			.card.bg-gray-dark .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.second:hover,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table thead tr:first-child th:hover,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.day:hover,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.hour:hover,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.minute:hover,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.second:hover {
				background-color: #222629;
				color: #fff;
			}

			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.today::before,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.today::before {
				border-bottom-color: #fff;
			}

			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gray-dark .bootstrap-datetimepicker-widget table td.active:hover,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.active,
			.card.bg-gradient-gray-dark .bootstrap-datetimepicker-widget table td.active:hover {
				background-color: #4b545c;
				color: #fff;
			}

			.card {
				box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);
				margin-bottom: 1rem;
			}

			.card.bg-dark .card-header {
				border-color: #383f45;
			}

			.card.bg-dark,
			.card.bg-dark .card-body {
				color: #fff;
			}

			.card.maximized-card {
				height: 100% !important;
				left: 0;
				max-height: 100% !important;
				max-width: 100% !important;
				position: fixed;
				top: 0;
				width: 100% !important;
				z-index: 1040;
			}

			.card.maximized-card.was-collapsed .card-body {
				display: block !important;
			}

			.card.maximized-card .card-body {
				overflow: auto;
			}

			.card.maximized-card [data-widget="collapse"] {
				display: none;
			}

			.card.maximized-card .card-header,
			.card.maximized-card .card-footer {
				border-radius: 0 !important;
			}

			.card.collapsed-card .card-body,
			.card.collapsed-card .card-footer {
				display: none;
			}

			.card .nav.flex-column>li {
				border-bottom: 1px solid rgba(0, 0, 0, 0.125);
				margin: 0;
			}

			.card .nav.flex-column>li:last-of-type {
				border-bottom: 0;
			}

			.card.height-control .card-body {
				max-height: 300px;
				overflow: auto;
			}

			.card .border-right {
				border-right: 1px solid rgba(0, 0, 0, 0.125);
			}

			.card .border-left {
				border-left: 1px solid rgba(0, 0, 0, 0.125);
			}

			.card.card-tabs:not(.card-outline)>.card-header {
				border-bottom: 0;
			}

			.card.card-tabs:not(.card-outline)>.card-header .nav-item:first-child .nav-link {
				border-left-color: transparent;
			}

			.card.card-tabs.card-outline .nav-item {
				border-bottom: 0;
			}

			.card.card-tabs.card-outline .nav-item:first-child .nav-link {
				border-left: 0;
				margin-left: 0;
			}

			.card.card-tabs .card-tools {
				margin: .3rem .5rem;
			}

			.card.card-tabs:not(.expanding-card).collapsed-card .card-header {
				border-bottom: 0;
			}

			.card.card-tabs:not(.expanding-card).collapsed-card .card-header .nav-tabs {
				border-bottom: 0;
			}

			.card.card-tabs:not(.expanding-card).collapsed-card .card-header .nav-tabs .nav-item {
				margin-bottom: 0;
			}

			.card.card-tabs.expanding-card .card-header .nav-tabs .nav-item {
				margin-bottom: -1px;
			}

			.card.card-outline-tabs {
				border-top: 0;
			}

			.card.card-outline-tabs .card-header .nav-item:first-child .nav-link {
				border-left: 0;
				margin-left: 0;
			}

			.card.card-outline-tabs .card-header a {
				border-top: 3px solid transparent;
			}

			.card.card-outline-tabs .card-header a:hover {
				border-top: 3px solid #dee2e6;
			}

			.card.card-outline-tabs .card-header a.active:hover {
				margin-top: 0;
			}

			.card.card-outline-tabs .card-tools {
				margin: .5rem .5rem .3rem;
			}

			.card.card-outline-tabs:not(.expanding-card).collapsed-card .card-header {
				border-bottom: 0;
			}

			.card.card-outline-tabs:not(.expanding-card).collapsed-card .card-header .nav-tabs {
				border-bottom: 0;
			}

			.card.card-outline-tabs:not(.expanding-card).collapsed-card .card-header .nav-tabs .nav-item {
				margin-bottom: 0;
			}

			.card.card-outline-tabs.expanding-card .card-header .nav-tabs .nav-item {
				margin-bottom: -1px;
			}

			html.maximized-card {
				overflow: hidden;
			}

			.card-header::after,
			.card-body::after,
			.card-footer::after {
				display: block;
				clear: both;
				content: "";
			}

			.card-header {
				background-color: transparent;
				border-bottom: 1px solid rgba(0, 0, 0, 0.125);
				padding: 0.75rem 1.25rem;
				position: relative;
				border-top-left-radius: 0.25rem;
				border-top-right-radius: 0.25rem;
			}

			.collapsed-card .card-header {
				border-bottom: 0;
			}

			.card-header>.card-tools {
				float: right;
				margin-right: -0.625rem;
			}

			.card-header>.card-tools .input-group,
			.card-header>.card-tools .nav,
			.card-header>.card-tools .pagination {
				margin-bottom: -0.3rem;
				margin-top: -0.3rem;
			}

			.card-header>.card-tools [data-toggle="tooltip"] {
				position: relative;
			}

			.card-title {
				float: left;
				font-size: 1.1rem;
				font-weight: 400;
				margin: 0;
			}

			.card-text {
				clear: both;
			}

			.btn-tool {
				background-color: transparent;
				color: #adb5bd;
				font-size: 0.875rem;
				margin: -0.75rem 0;
				padding: .25rem .5rem;
			}

			.btn-group.show .btn-tool,
			.btn-tool:hover {
				color: #495057;
			}

			.show .btn-tool,
			.btn-tool:focus {
				box-shadow: none !important;
			}

			.text-sm .card-title {
				font-size: 1rem;
			}

			.text-sm .nav-link {
				padding: 0.4rem 0.8rem;
			}

			.card-body>.table {
				margin-bottom: 0;
			}

			.card-body>.table>thead>tr>th,
			.card-body>.table>thead>tr>td {
				border-top-width: 0;
			}

			.card-body .fc {
				margin-top: 5px;
			}

			.card-body .full-width-chart {
				margin: -19px;
			}

			.card-body.p-0 .full-width-chart {
				margin: -9px;
			}

			.chart-legend {
				padding-left: 0;
				list-style: none;
				margin: 10px 0;
			}

			@media (max-width: 576px) {
				.chart-legend>li {
					float: left;
					margin-right: 10px;
				}
			}

			.card-comments {
				background-color: #f8f9fa;
			}

			.card-comments .card-comment {
				border-bottom: 1px solid #e9ecef;
				padding: 8px 0;
			}

			.card-comments .card-comment::after {
				display: block;
				clear: both;
				content: "";
			}

			.card-comments .card-comment:last-of-type {
				border-bottom: 0;
			}

			.card-comments .card-comment:first-of-type {
				padding-top: 0;
			}

			.card-comments .card-comment img {
				height: 1.875rem;
				width: 1.875rem;
				float: left;
			}

			.card-comments .comment-text {
				color: #78838e;
				margin-left: 40px;
			}

			.card-comments .username {
				color: #495057;
				display: block;
				font-weight: 600;
			}

			.card-comments .text-muted {
				font-size: 12px;
				font-weight: 400;
			}

			
		</style>

		<?php
		 	// -- CSS -- 

			// echo '<link href="../../tool/bootstrap-5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>';
			echo '<link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet"/>';
			echo '<link rel="stylesheet" href="../../css/bootstrap.css">';

			echo '<link rel="stylesheet" href="../../css/registration.css">';
			echo '<link rel="stylesheet" href="../../css/sidebar.css">';

			echo '<script src="../../tool/jquery-3.6.0.min.js"></script>';
			echo '<script src="https://code.iconify.design/2/2.2.1/iconify.min.js"></script>';

			// // -- JS -- 

			echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"/>';
			echo '<script src="../../tool/bootstrap-5.0.2/dist/js/bootstrap.bundle.min.js"></script>';
			echo '<script src="../../js/functions.js"></script>';
		?>
		<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" />
		<script src="https://cdn.datatables.net/fixedcolumns/4.2.1/js/dataTables.fixedColumns.min.js"></script>
		
		<style>
			.custom-modal {
				display: none; /* Hidden by default */
				position: fixed; /* Stay in place */
				z-index: 1000; /* On top */
				left: 0;
				top: 0;
				width: 100%; /* Full width */
				height: 100%; /* Full height */
				background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
			}

			/* Modal content */
			.custom-modal-content {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				background-color: #fff;
				padding: 20px;
				text-align: center;
				border-radius: 10px;
				box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
			}

			.loading-gif {
				margin-bottom: 10px;
			}

			/* Loading message */
			.modal-content p {
				font-size: 18px;
				color: #333;
				margin: 0;
			}

			/* Success and Fail Icon */
			#alertIcon {
				display: block;
				font-size: 40px;
				margin-bottom: 10px;
			}

			.content{
				align-items:center;
			}

			.icon{
				padding: 0.5rem;
				margin-right: 1rem;
				border-radius:39% 61% 42% 58% / 50% 51% 49% 50%;
			}
			.close{
				background-color: transparent;
				border:none;
				outline:none;
				transition:all 0.2s ease-in-out;
				padding: 0.75rem;
				border-radius:0.5rem;
				cursor:pointer;
				display: flex;
				align-items:center;
				justify-content: center;
			}
			.close:hover{
				background-color: #fff;
			}

			.success{
				background-color: rgba(62, 189, 97);
				border:2px solid #3ebd61;
			}
			.success .icon{
				background-color:#3ebd61;
			}
			.info{
				background-color: rgba(0, 108, 227);
				border:2px solid #006CE3;
			}
			.info .icon{
				background-color: #006CE3;
			}
			.warning{
				background-color: rgba(239, 148, 0);
				border:2px solid #EF9400;
			}
			.warning .icon{
				background-color: #EF9400;
			}

			.danger{
				background-color: rgba(236, 77, 43);
				border:2px solid #EF9400;
			}
			.danger .icon{
				background-color: #EC4D2B;
			}

			.valid {
				width: 100%;
				padding: 12px 16px;
				border-radius: 4px;
				border-style: solid;
				border-width: 1px;
				margin-bottom: 12px;
				font-weight : bold;
				background-color: rgba(227, 253, 235, 1);
				border-color: rgba(38, 179, 3, 1);
				color: rgba(60, 118, 61, 1);
			}
			.invalid {
				width: 100%;
				padding: 12px 16px;
				border-radius: 4px;
				border-style: solid;
				border-width: 1px;
				margin-bottom: 12px;
				font-weight : bold;
				background-color: rgba(248, 215, 218, 1);
				border-color: rgba(220, 53, 69, 1);
				color: rgba(114, 28, 36, 1);
			}

		</style>

	</head>

	<?php include '../../header.html';?>

	<body id="body-pd">

		<?php include 'sidebar.php';?>

		<div class="container-fluid">

			<div id="loadingModal" class="custom-modal">
				<div class="custom-modal-content">
					<img src="../../image/Loader8.gif" alt="Loading..." class="loading-gif">
					<p>Loading, please wait...</p>
				</div>
			</div>

			<div id="alertModal" class="custom-modal">
				<div class="custom-modal-content">
					<span id="alertIcon"></span>
					<p id="alertMessage"></p>
				</div>
			</div>

			<div id="ess-home-div">
				<br><br><br>
				<div class="card card-outline card-primary">
					<div class="card-body">
						<section class="container" id="section-datapolicy-container">
							<div class="col" style="padding-block: 1rem;">
								<div class="col" >
									<div class="datacont" style='margin-inline: 1rem; color: black; text-align: left; padding: 0rem;'>
										<div style="background: url('../../image/FCPC LOGO.png') no-repeat center 50%; background-size: contain;">
											<h1> <center> Data Privacy Policy </center></h1>
											
											<div style='overflow-y: auto; max-height: 75vh;'>
												<i style="font-size: 24px">We care about your Privacy.</i><br><br>

												In order to ensure compliance with Republic Act No. 10173 otherwise known as the “Data Privacy Act of 2012”, we came up with a privacy policy that embodies how FIRST CITY PROVIDENTIAL COLLEGE INC., uses and protects any information that you provide through this application.<br><br>

												This Privacy Policy ensures that all sensitive information/personal data that you provide whenever this website is accessed shall be fully protected and will only be utilized in accordance with this Privacy Policy.<br><br>
												
											</div>					
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					
				</div>
			</div>

			<div id="ess-staff-div" hidden>
				<?php include 'forms/ess-staff-form.php'; ?>
			</div>

			<div id="ess-teamleader-div" hidden>
				<?php include 'forms/ess-teamleader-form.php'; ?>
			</div>

			<div id="ess-finance-div" hidden>
				<?php include 'forms/ess-finance-form.php'; ?>
			</div>

		</div>


	</body>

	<?php include '../../footer.html';?>

	<?php 
		echo '<script src="../../js/sidebar.js"></script>';
		echo '<script src="../../js/main-dashboard.js"></script>';
	?>



</html>