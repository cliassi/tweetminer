<?php
	//Globals
	$date = date("Y-m-d",time());
	$date1 = date("d-M-Y",time());
	$time = date("H:m:s", time());
	$events = array('li'=>1, 'lo'=>2, 'st'=>3, 'fa'=>4, 'ds'=>5, 'di'=>6, 'du'=>7, 'dd'=>8);
	$sum = array();
	$counta = array();
	$registry = array();
	function back(){
		print "<div class='cntr'><a href='javascript:window.history.back()'><img src='ico/back.png' /> Back </a></div>";
	}
	//function
	//application related
	function menuUpdated(){
		deleteAllFiles('temps/menus/');
	}
	/*function ralert($object, $func, $id){
		insert("alt_alert", "al_object, al_id, al_viewed_by, al_time, al_func", "'$object', $id, ".uid().", '".now()."', '$func'");
	}*/
	function ralert(){
		insert("alt_alert", "al_object, al_id, al_viewed_by, al_time, al_func", "'".$_GET['q']."', '".(isset($_GET['id'])?$_GET['id']:'')."', ".uid().", '".now()."', '".(isset($_GET['f'])?$_GET['f']:'')."'");
	}
	function module($title, array $hearer, $data, $span=5){
		$con = "<div class='widget-box span$span'>";
			$con .= '<div class="widget-header widget-header-flat">';
				$con .= '<h4 class="lighter">';
					$con .= '<i class="icon-star orange"></i>';
						$con .= $title;
				$con .= '</h4>';
		
				$con .= '<div class="widget-toolbar">';
					$con .= '<a href="#" data-action="collapse">';
						$con .= '<i class="icon-chevron-up"></i>';
					$con .= '</a>';
				$con .= '</div>';
			$con .= '</div>';
		
			$con .= '<div class="widget-body">';
				$con .= '<div class="widget-main no-padding">';
					$con .= '<table class="table table-bordered table-striped">';
						$con .= '<thead>';
							$con .= '<tr>';
								for($i = 0; $i < count($hearer); $i++)
								{
									$con .= '<th>';	
										$con .= '<i class="icon-caret-right blue"></i>';
										$con .= $hearer[$i];
									$con .= '</th>';	
								}
							$con .= '</tr>';
						$con .= '</thead>';
		
						$con .= '<tbody>';
							while($rows = mysqli_fetch_object($data)){
							$con .= '<tr>';
								foreach($rows as $cell)
								{
									$con .= '<td>';
									$con .= "$cell";
									$con .= '</td>';	
								}
							$con .= '</tr>';
							}
						$con .= '</tbody>';
					$con .= '</table>';
				$con .= '</div><!--/widget-main-->';
			$con .= '</div><!--/widget-body-->';
		$con .= '</div><!--/widget-box-->';
		
		print $con;
	}
	function oRow(){
		print '<div class="row-fluid">';
	} 
	
	function cRow(){
		print '</div>';
	}
	
	// show upload form, if file not available
	function fon($dir, $file, $w='', $h=''){
		createDir($dir);
		if(isset($_POST['upload'])){
			if(file_exists($dir."/".$file)) unlink($dir."/".$file);
			upload($_FILES, $file, $dir);
			sleep(10);
			redir("?".uri());
		}
		if(file_exists($dir."/".$file.".png")){
			$con = "<img src='".$dir."/".$file.".png' ".nn($w, "width='{$w}px'")." ".nn($h, "height='{$h}px'")." />";
		}	else{
			$con = "<form method='post' enctype='multipart/form-data' />";
			$con .= "<img src='ico/noimage.png' /><br /><br />";
			$con .= "<input type='file' name='file' class='required' /> <input type='submit' class='upload' name='upload' value=''  /></form>";
		}
		return $con;
	}
	
	function widget($title = 'Untitled', $body = '', $span = 5, $height = 200){
		print "<div class='span$span'>
			<div class='widget-box'>
				<div class='widget-header widget-header-flat widget-header-small'>
					<h5>
						<i class='icon-info-sign'></i>
						$title
					</h5>
				</div>

				<div class='widget-body'>
					<div class='widget-main h$height'>
						$body
					</div>
				</div>
			</div>
		</div>";
	}
	
	function widget2($body = '', $span=6){
		print "<div class='span$span' style='padding:5px; background:rgba(245,245,255,.4); border: dotted 1px #d8d8d8!important'>$body</div>";
	}
	function getBalance($b, $bi, $date = ''){
		$date = nn($date)?"'$date'":"DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
		$ob = select("SELECT getOpeningBalanceById('Debit Note', '$b', $bi, $date) dn, 
				getOpeningBalanceById('Credit Note', '$b', $bi, $date) cn,
				getOpeningBalanceById('Purchase Order', '$b', $bi, $date) po,
				getOpeningBalanceById('Payment Voucher', '$b', $bi, $date) pv,
				getOpeningBalanceById('Invoice', '$b', $bi, $date) inv,
				getOpeningBalanceById('Sales Receipt', '$b', $bi, $date) sr,
				getOpeningBalanceById('Commission', '$b', $bi, $date) com");
				$o = mfo($ob);  	
  	$opening_balance = $o->dn - $o->cn + $o->po - $o->pv - $o->inv + $o->sr + $o->com;
		return $opening_balance;
	}
	function formatId($id, $type, $date, $link = false, $option='print', $length = 4, $prefix = 'SL'){
		if(strlen($type)>3){
			switch ($type){
				case 'Credit Note': { $type = 'CN';  $page = "sales_credit_note"; } break;
				case 'Debit Note': { $type = 'DN';  $page = "purchase_note"; } break;
				case 'Purchase Order': { $type = 'PO';  $page = "purchase_order"; } break;
				case 'Sales Quotation': { $type = 'RQ';  $page = "sales_quotation"; } break;
				case 'Quotation': { $type = 'SQ';  $page = "quotation"; } break;
				case 'Sales Order': { $type = 'SO';  $page = "sales_order"; } break;
				case 'Purchase Batch': { $type = 'PB';  $page = "purchase_batch"; } break;
				case 'Payment Voucher': { $type = 'PV';  $page = "purchase_payment_voucher"; } break;
				case 'Sales Receipt': { $type = 'OR';  $page = "sales_receipt"; } break;
				case 'Invoice': { $type = 'INV';  $page = "sales_invoice"; } break;
				case 'Delivery': { $type = 'DO';  $page = "sales_delivery_order"; } break;
				case 'Purchase Requisition': { $type = 'PR';  $page = "purchase_requisition"; } break;
			}
		}
		if(!nn($date)){ $date = today(); }
		$str = "$prefix/".strtoupper($type)."/".zerofill($id, $length)."/".date("Ymd", strtotime($date));
		if($link) $str = "<a href='$page?f=$option&id=$id'>$str</a>";
		return $str;
	}
	function userList($filter=""){
		$list = array();
		$users = select("*", "sys_user", $filter);
		while($u = mysqli_fetch_object($users)){
			$list[$u->id] = $u->u_fullname;	
		}
		return $list;	
	}
	function openFilterForm($method = 'post'){
		print "<div align='center'><form method='$method' id='filter-form'>";
	}
	
	function closeFilterForm(){
		print "<input type='submit' value='Filter' /></form></div>";	
	}
	function openForm($method = 'post', $upload = false){
		print "<form method='$method'".($upload?" enctype='multipart/form-data'":"").">";
	}
	
	function closeForm($submit_button_value = 'Save'){
		print "<br /><div align='center'><input type='submit' name='save' value='$submit_button_value' style='width:80px;height:25px;' /></div></form>";	
	}
	function ot($width="", $class="grid", $id="", $style=""){ // Open Table tag
		print "<table class='$class' ".($width?"width='$width'":"")." ".($id?"id='$id'":"")." ".($style?"style='$style'":"").">";	
	}
	function ct($class="grid", $width=""){	// Close Table Tag
		print "</table>";	
	}
	function name($field){
		$names = explode("_", $field);
		$name = "";
		$i = 0;
		if(count($names)==1) return $field;
		foreach($names as $n){
			if($i++==0) continue;
			$name .= $name!=""?"_":"";
			$name .= $n;
			$i++;		
		}
		return $name;
	}
	function title($field){
		$names = explode("_", ucfirst(trim($field)));
		$name = "";
		$i = 0;
		if(count($names)==1) return $field;
		foreach($names as $n){
			if($i++==0) continue;
			$name .= $name!=""?" ":"";
			$name .= $n;
			$i++;		
		}
		return ucwords($name);
	}
	function title2($field){
		$names = explode("_", ucfirst(trim($field)));
		$name = "";
		$i = 0;
		if(count($names)==1) return $field;
		foreach($names as $n){
			$name .= $name!=""?" ":"";
			$name .= $n;
			$i++;		
		}
		return ucwords($name);
	}
	function clear($elm){
		return '<img src="ico/clear_gray.png" width="16px" onclick="$(\''.$elm.'\').val(\'\')" />';
	}
	function feed($name, $data, $url, $method = 'post'){
		print "<form method='$method' id='feed-form' action='$url'><input type='hidden' name='$name' value='$data' /></from><script type='text/javascript'>\$('#feed-form').submit();</script>";
	}
	function addToolBox($name){
		print "<div class='toolbox curved-right' id='$name'>";
		include("system/tools/$name.php");	
		print "</div>";	
		print "<script type='text/javascript'>
				$('.toolbox-item').draggable({connectToSortable: '#editing-area',  	revert: 'invalid'});
				</script>";
	}
	function gridView($name, $headings, $data, $cells, $width = "100%"){
		echo "<table class='grid' width='$width'>";
		echo "<tr>";
		foreach($headings as $heading){
			echo "<th>$heading</th>";
		}
		echo "</tr>";
		echo "<tr>";
		while($row = mysqli_fetch_object($data)){
			foreach($cells as $cell){
				echo "<td>".$row->$cell."</td>";
			}
		}
		echo "<td>".options('','')."</td></tr>";
		echo "</table>";
	}
	function uri($replace = '', $with = '', $remove = array()){
		$uri = $_SERVER['QUERY_STRING'];
		$not_in = true;
		$uri = explode("&", $uri);
		$ret_uri = "";
		foreach($uri as $q){
			$qe = explode("=", $q);
			if($qe[0] != "q"){
				if($replace != '' && $qe[0] == $replace){
					$ret_uri .= "$qe[0]=$with&";
					$not_in = false;
				} else{
					if(!in_array($qe[0], $remove)){
						$ret_uri .= $q."&";
					}
				}
			}			
		}
		if($not_in){
			$ret_uri .= "$replace=$with&";
		}
		return substr($ret_uri,0,-1);
	}
	
	function muri($uri='', $replace = array(), $value = array(), $remove = array()){
		$uri = $uri==''?$_SERVER['QUERY_STRING']:$uri;
		$uri = explode("&", $uri);
		$ret_uri = "";
		$i = 0;
		foreach($uri as $u){
			$qe = explode("=", $u);
			if($qe[0] == 'q') continue;
			if(in_array($qe[0], $remove)){
			} elseif(in_array($qe[0], $replace)){
				$ret_uri .= ($ret_uri != ""?"&":"")."$qe[0]=".$value[$i++];
				$index = array_search($qe[0], $replace);
				unset($replace[$index]);
				unset($value[$index]);
			} else{
				$ret_uri .= ($ret_uri != ""?"&":"")."$qe[0]".(isset($qe[1])?"=$qe[1]":'');
			}
		}
		$i = 0;
		$value = array_values($value);
		foreach($replace as $re){
			$ret_uri .= ($ret_uri != ""?"&":"")."$re=".$value[$i++];
		}
		return $ret_uri;
	}
	function fs($words = array(), $separator = " "){
		$return = "";
		foreach($words as $word){
			if(nn($word)){
				$return .= $word.$separator;
			}
		}
		return nn($return)?substr($return, 0, -strlen($separator)):$return;
	}
	function fn($fname, $mname = '', $lname ='', $op1 ='', $op2 =''){ //format name		
		$name = $fname;
		if($mname <> ""){
			$name .= " $mname";
		}
		if($lname <> ""){
			$name .= " $lname";
		}
		if($op1 <> ""){
			$name .= " $op1";
		}
		if($op2 <> ""){
			$name .= " $op2";
		}
		return $name;
	}
	$accessGranted = array();
	function mlink($text,$object,$option, $id=false){
		global $accessGranted;
		if(in_array($object.$option, $accessGranted)){
			$text = "<a href='$object?f=$option".($id?"&id=$id":"")."'>$text</a>";
		} elseif(hasAccess($object, $option)){
			$text = "<a href='$object?f=$option".($id?"&id=$id":"")."'>$text</a>";
			array_push($accessGranted, $object.$option);
		}
		return $text;		
	}
	function hasAccess($object, $option){
		global $accessGranted;
		if(uid()==1){
			return true;
		} elseif(in_array($object.$option, $accessGranted)){
			return true;
		} else{
			$options = select("DISTINCT `option`, icon", "sys_permission", "link='$object' AND `option`='$option' AND user=".uid());
			if($options->num_rows>0){
				array_push($accessGranted, $object.$option);
				return true;
			} else{
				return false;
			}
		}
		return false;
	}
	
	function controllers($object = '', $not = array(), $size = '24'){
		$object = $object?$object:$_GET['q'];
		$ret_str = "";
		if(uid()==1){
			$options = select("DISTINCT `option`, icon", "sys_privilege", "link='$object' AND controller=1");
		} else{
			$options = select("DISTINCT `option`, icon", "sys_permission", "link='$object' AND controller=1 AND user=".uid());
		}
		while($ops = mysqli_fetch_object($options)){
			if(!in_array($ops->option, $not)){
				$ret_str .= "<a class='nfp' href='$object?f=$ops->option";
				$ret_str .= "'><img width='".$size."px' height='".$size."px' src='ico/$ops->icon' /></a> ";
			}
		}
		return $ret_str;
	}
	function options($object = '', $id = false, $not = array(), $size = '16', $button = false){
		$object = $object?$object:$_GET['q'];
		$ret_str = "";
		if(uid()==1){
			$options = select("DISTINCT `option`, icon, title, target", "sys_privilege", "link='$object'");
		} else{
			$options = select("DISTINCT `option`, icon, title, target", "sys_permission", "link='$object' AND user=".uid());
		}
		while($ops = mysqli_fetch_object($options)){
			if(!in_array($ops->option, $not)){
				if($button){
					$ret_str .= "<input type='button' onclick='redir(\"$object?f=$ops->option";
					if($id){
						$ret_str .= "&id=$id";	
					}
					$ret_str .= "\")' value='$ops->title' />";
				} else{
					$ret_str .= "<a title='$ops->title' target='$ops->target' href='$object?f=$ops->option";
					if($id){
						$ret_str .= "&id=$id";	
					}
					$ret_str .= "'><img width='".$size."px' height='".$size."px' src='ico/$ops->icon' /></a> ";
				}
			}
		}
		return $ret_str;
	}
	function options2($object = '', $id = false, $in = array(), $size = '16', $button = false){
		$object = $object?$object:$_GET['q'];
		$ret_str = "";
		if(uid()==1){
			$options = select("DISTINCT `option`, icon, title, target", "sys_privilege", "link='$object'");
		} else{
			$options = select("DISTINCT `option`, icon, title, target", "sys_permission", "link='$object' AND user=".uid());
		}
		while($ops = mysqli_fetch_object($options)){
			if(in_array($ops->option, $in)){if($button){
					$ret_str .= "<input type='button' onclick='redir(\"$object?f=$ops->option";
					if($id){
						$ret_str .= "&id=$id";	
					}
					$ret_str .= "\")' value='$ops->title' />";
				} else{
					$ret_str .= "<a title='$ops->title' target='$ops->target' href='$object?f=$ops->option";
					if($id){
						$ret_str .= "&id=$id";	
					}
					$ret_str .= "'><img width='".$size."px' height='".$size."px' src='ico/$ops->icon' /></a> ";
				}
			}
		}
		return $ret_str;
	}
	function now(){
		return date("Y-m-d H:i:s", time());	
	}
	function today(){
		return date("Y-m-d", time());	
	}
	function ctime(){
		return date("H:i:s", time());	
	}
	function daydiff($ds, $de){
		$dStart = new DateTime($ds);
		$dEnd  = new DateTime($de);
		////echo $dDiff->format('%R'); // use for point out relation: smaller/greater
		$dDiff = $dStart->diff($dEnd);
		return $dDiff->days;
	}
	function getName($type, $id){
		$name = "";
		if($type=="Carrier"){
			$name = getFieldValue("_carrier", "com_name", "id=$id");
		} elseif($type=="Agent"){
			$name = getFieldValue("_agent", "p_name", "id=$id");
		} elseif($type=="Individual"){
			$name = getFieldValue("_customer", "p_name", "id=$id");
		} elseif($type=="Customer"){
			$name = getFieldValue("_customer", "p_name", "id=$id");
		} elseif($type=="Expense"){
			$name = getFieldValue("acc_expense_category", "ec_name", "id=$id");
		}
		return $name;
	}
	
	function rec_debit($amount, $source, $ref){
		insert("transactions", "t_time, t_user, t_amount, r_source, t_ref, t_type, t_payment_type, t_first_payment", "NOW(), ".userid().", '$amount', '$source', '$ref', 'Debit', '$type', $firstpayment");
	}
	function pageBreak(){
		echo '<div class="page-break"></div>';	
	}
	//Date format
	function dt($time){
		return (trim($time)!="" && $time!=null && $time!='0000-00-00' && $time!='0000-00-00 00:00:00') ? date("Y-m-d", strtotime($time)) : false;
	}
	function df($time){
		return (trim($time)!="" && $time!=null && $time!='0000-00-00' && $time!='0000-00-00 00:00:00') ? date("d M, Y", strtotime($time)) : false;
	}
	//Number format
	function nf($num, $digit=2){
		$num = number_format($num, $digit);
		return $num;
	}
	function isMySQLFunc($func){
		$funcs = array('CURTIME()', 'CURDATE()', 'NOW()');
		if(in_array($func, $funcs)){return true;}
		return false;
	}	
	function is($name, $default = "", $method = ""){	//wrapper isset()
		if($method == "post"){
			$default = isset($_POST[$name])?$_POST[$name]:$default;
		} elseif($method == "get"){
			$default = isset($_GET[$name])?$_GET[$name]:$default;
		} else{
			$default = isset($_REQUEST[$name])?$_REQUEST[$name]:(isset($_GET['q'])?(isset($_SESSION[$_GET['q']."_$name"])?$_SESSION[$_GET['q']."_$name"]:$default):$default);
		}
		if(isset($_GET['q'])){
			$default = $_SESSION[$_GET['q']."_$name"] = isset($_REQUEST[$name])?$_REQUEST[$name]:$default;
		}
		return $default;
	}
	function is2($name, $default = "", $method = ""){	//wrapper isset()
		if($method == "post"){
			$default = isset($_POST[$name])?$_POST[$name]:$default;
		} elseif($method == "get"){
			$default = isset($_GET[$name])?$_GET[$name]:$default;
		} else{
			$default = isset($_REQUEST[$name])?$_REQUEST[$name]:$default;
		}
		return $default;
	}
	function adminonly(){
		if($_SESSION[APP.'role']!=2){
			die("<h3>Admin area!</h3><h2>You must have admin privilege to access this page!</h2>");
		}
	}
	function useronly(){
		if($_SESSION[APP.'role']<2){
			die("<h2>You must be logged in as a user to access this page!</h2>");
		}
	}
	function roleonly($role=1){
		if(is_numeric($role)) {$name = getFieldValue("role", "name", "id='$role'");} 
			else {$name = $role; $role = getFieldValue("role", "id", "name='$role'");}
		if($_SESSION[APP.'role']!=$role){
			die("<h2>You must be logged in as a(an) <i>$name</i> to access this page!</h2>");
		}
	}
	function ordinal($num){
		$num = $num + 0;
		if(is_int($num)){
			$last_digit = substr($num,strlen($num."")-1);
			if($num < 1){
				return $num."th";
			} elseif($last_digit == 1){;
				return $num."st";
			} elseif($last_digit == 2){
				return $num."nd";
			} elseif($last_digit == 3){
				return $num."rd";
			} else{
				return $num."th";
			}
		}
		return "";
	}
	function staffId(){
		$emp = select("*", "user_staff", "user_id=".uid()."");
		if($emp){
			if($emp->num_rows){
				$e = mfo($emp);	
				return $e->staff_id;
			} else{
				return 0;
			}
		}
		return 0;
	}
	function cuid(){
		return getFieldValue("sales_customer", "id", "c_user_id=".uid());
	}
	function suid(){
		return getFieldValue("purchase_supplier", "id", "s_user_id=".uid());
	}
	function uid(){
		return $_SESSION[APP.'id'];
	}
	function loggedin(){
		return isset($_SESSION[APP.'loggedin'])?true:false;
	}
	function rid(){
		return getFieldValue("sys_user_role", "ur_role_id", "ur_user_id=".uid());
	}
	function site(){
		return isset($_GET['site'])?$_GET['site']:(isset($_SESSION[APP.'site'])?$_SESSION[APP.'site']:DEFAULT_SITE);
	}
	function sitename($id = false){
		if($id){
			return getFieldValue("sys_sites", "s_name", "id=$id");
		} else{
			return $_SESSION[APP.'sitename'];
		}
	}
	function rolename($id=''){
		$rid = $id!=''?$id:rid();
		return getFieldValue("sys_role", "r_name", "id=$rid");
	}
	function bid(){
		return $_SESSION[APP.'branch'];
	}
	function username(){
		return $_SESSION[APP.'fullname'];
	}
	function isDate($date){
		return preg_match('/^[0-9]{4}\-(0[1-9]|1[0-2])\-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date);
	}
	function dateSelector($name, $sday='', $smon='', $syear=''){
		if($sday!='' && $smon==''){
			$syear = substr($sday,0,4);
			$smon = substr($sday,5,2);
			$sday = substr($sday,8,2);	
		}
		$data = createSelectOption("id='".$name."_day' class='dateselector' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1, 31, $sday==''?date("d", time()):$sday, 2);
		$data .= "<select id='".$name."_mon' class='dateselector' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'>".getMonthList($smon==''?date("m", time()):$smon)."</select>";
		$data .= createSelectOption("id='".$name."_year' class='dateselector' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1930, date("Y", time())+30, $syear==''?date("Y", time()):$syear);
		$data .= "<input type='hidden' class='dateselectorvalue' name='$name' id='$name' />
			<script type='text/javascript'>
				setDate(\"$name\");
				function setDate(obj){
					$('#'+obj).val($('#'+obj+'_year').val()+'-'+$('#'+obj+'_mon').val()+'-'+$('#'+obj+'_day').val());
				}
			</script>";
		return $data;
	}
	function dateSelectorJS($name, $sday='', $smon='', $syear=''){
		if($sday!='' && $smon==''){
			$syear = substr($sday,0,4);
			$smon = substr($sday,5,2);
			$sday = substr($sday,8,2);	
		}
		$data = createSelectOption("id='".$name."_day' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1, 31, $sday==''?date("d", time()):$sday, 2);
		$data .= "<select id='".$name."_mon' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'>".getMonthList($smon==''?date("m", time()):$smon)."</select>";
		$data .= createSelectOption("id='".$name."_year' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1930, date("Y", time())+30, $syear==''?date("Y", time()):$syear);
		$data .= "<input type='hidden' name='$name' id='$name' />";
		return $data;
	}
	function datetimeSelector($name, $time=false, $num=''){
		if(!$time){
			$time = date("Y-m-d H:i:s", time());
		}
		$syear = date("Y", strtotime($time));
		$smon = date("m", strtotime($time));
		$sday = date("d", strtotime($time));
		$shour = date("H", strtotime($time));
		$smin = date("i", strtotime($time));
		$ssec = date("s", strtotime($time));
		$data = createSelectOption("id='".$name.$num."_day' class='datetimeselector' onChange='setDate(\"$name$num\")' onKeyUp='setDate(\"$name$num\")'", 1, 31, $sday==''?date("d", time()):$sday, 2);
		$data .= "<select id='".$name.$num."_mon' class='datetimeselector' onChange='setDate(\"$name$num\")' onKeyUp='setDate(\"$name$num\")'>".getMonthList($smon==''?date("m", time()):$smon, 2)."</select>";
		$data .= createSelectOption("id='".$name.$num."_year' class='datetimeselector' onChange='setDate(\"$name$num\")' onKeyUp='setDate(\"$name$num\")'", 1930, date("Y", time())+30, $syear==''?date("Y", time()):$syear);
		$data .= " - ".createSelectOption("id='".$name.$num."_hour' class='datetimeselector' onChange='setDate(\"$name$num\")' onKeyUp='setDate(\"$name$num\")'", 0, 23, $shour==''?date("H", time()):$shour, 2);
		$data .= ":".createSelectOption("id='".$name.$num."_min' class='datetimeselector' onChange='setDate(\"$name$num\")' onKeyUp='setDate(\"$name$num\")'", 0, 59, $smin==''?date("i", time()):$smin, 2);
		$data .= ":".createSelectOption("id='".$name.$num."_sec' class='datetimeselector' onChange='setDate(\"$name$num\")' onKeyUp='setDate(\"$name$num\")'", 0, 59, $ssec==''?date("s", time()):$ssec, 2);
		$data .= "<input type='hidden' class='datetimeselectorvalue' name='$name".($num?"[]":"")."' id='".$name.$num."' />
			<script type='text/javascript'>
				setDate(\"$name$num\");
				function setDate(obj){
					$('#'+obj).val($('#'+obj+'_year').val()+'-'+$('#'+obj+'_mon').val()+'-'+$('#'+obj+'_day').val()+' '+$('#'+obj+'_hour').val()+':'+$('#'+obj+'_min').val()+':'+$('#'+obj+'_sec').val());
				}
			</script>";
		return $data;
	}
	function timeSelector($name, $time='', $num=''){
		$time = $time==""?time():strtotime($time);
		$smin = date("i", $time);
		$ssec = date("s", $time);
		$shour = date("H", $time);
		$data = createSelectOption("id='".$name.$num."_hour' onChange='setTime(\"$name$num\")' onKeyUp='setTime(\"$name$num\")'", 0, 23, $shour==''?date("H", time()):$shour, 2);
		$data .= ":".createSelectOption("id='".$name.$num."_min' onChange='setTime(\"$name$num\")' onKeyUp='setTime(\"$name$num\")'", 0, 59, $smin==''?date("i", time()):$smin, 2);
		$data .= ":".createSelectOption("id='".$name.$num."_sec' onChange='setTime(\"$name$num\")' onKeyUp='setTime(\"$name$num\")'", 0, 59, $ssec==''?date("s", time()):$ssec, 2);
		$data .= "<input type='hidden' name='$name".($num?"[]":"")."' id='".$name.$num."' />
			<script type='text/javascript'>
				setTime(\"".$name.$num."\");
				function setTime(obj){
					$('#'+obj).val($('#'+obj+'_hour').val()+':'+$('#'+obj+'_min').val()+':'+$('#'+obj+'_sec').val());
				}
			</script>";
		return $data;
	}
	function datetimeSelector_v1($name, $sday='', $smon='', $syear='', $shour='', $smin='', $ssec=''){
		if($sday!='' && $smon==''){
			$syear = substr($sday,0,4);
			$smon = substr($sday,5,2);
			$shour = substr($sday,11,2);
			$smin = substr($sday,14,2);
			$ssec = substr($sday,17,2);
			$sday = substr($sday,8,2);	
		}
		$data = createSelectOption("id='".$name."_day' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1, 31, $sday==''?date("d", time()):$sday, 2);
		$data .= "<select id='".$name."_mon' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'>".getMonthList($smon==''?date("m", time()):$smon, 2)."</select>";
		$data .= createSelectOption("id='".$name."_year' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1930, date("Y", time())+30, $syear==''?date("Y", time()):$syear);
		$data .= " - ".createSelectOption("id='".$name."_hour' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 0, 23, $shour==''?date("H", time()):$shour, 2);
		$data .= ":".createSelectOption("id='".$name."_min' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 0, 59, $smin==''?date("i", time()):$smin, 2);
		$data .= ":".createSelectOption("id='".$name."_sec' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 0, 59, $ssec==''?date("s", time()):$ssec, 2);
		$data .= "<input type='hidden' name='$name' id='$name' />
			<script type='text/javascript'>
				setDate(\"$name\");
				function setDate(obj){
					$('#'+obj).val($('#'+obj+'_year').val()+'-'+$('#'+obj+'_mon').val()+'-'+$('#'+obj+'_day').val()+' '+$('#'+obj+'_hour').val()+':'+$('#'+obj+'_min').val()+':'+$('#'+obj+'_sec').val());
				}
			</script>";
		return $data;
	}
	function timeSelector_v1($name, $shour='', $smin='', $ssec=''){
		if($shour!='' && $smin==''){
			$smin = substr($shour,3,2);
			$ssec = substr($shour,6,2);
			$shour = substr($shour,0,2);
		}
		$data = " - ".createSelectOption("id='".$name."_hour' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 0, 23, $shour==''?date("H", time()):$shour, 2);
		$data .= ":".createSelectOption("id='".$name."_min' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 0, 59, $smin==''?date("i", time()):$smin, 2);
		$data .= ":".createSelectOption("id='".$name."_sec' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 0, 59, $ssec==''?date("s", time()):$ssec, 2);
		$data .= "<input type='hidden' name='$name' id='$name' />
			<script type='text/javascript'>
				setDate(\"$name\");
				function setDate(obj){
					$('#'+obj+'_hour').val()+':'+$('#'+obj+'_min').val()+':'+$('#'+obj+'_sec').val());
				}
			</script>";
		return $data;
	}
	function dateSelector3($name, $sday='', $smon='', $syear=''){
		$data = createSelectOption("id='".$name."_day' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1, 31, $sday==''?date("d", time()):$sday);
		$data .= "<select id='".$name."_mon' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'>".getMonthList($smon==''?date("m", time()):$smon)."</select>";
		$data .= createSelectOption("id='".$name."_year' onChange='setDate(\"$name\")' onKeyUp='setDate(\"$name\")'", 1930, date("Y", time())+30, $syear==''?date("Y", time()):$syear);
		$data .= "<input type='hidden' name='$name' id='$name' />
			<script type='text/javascript'>
				setDate(\"$name\");
				function setDate(obj){
					$('#'+obj).val($('#'+obj+'_year').val()+'-'+$('#'+obj+'_mon').val()+'-'+$('#'+obj+'_day').val());
				}
			</script>";
		return $data;
	}
	function getMonthList($select = 1, $min = 0, $max = 0, $include_year = false){
		$month = "";
		for($i=($min?$min:1);$i<=($max?$max:12);$i++){
			if($i<=12){
				$mon = $i;
				$year = date("Y", time());
			} else{
				$mon = $i-12;				
				$year = date("Y", time()) + ceil($mon/12);
			}
			$month .= "<option value='".($include_year?"$year-":"").zerofill($mon,2).($include_year?"-1":"")."' ".($select==$mon?'selected':'').">".date("M".($include_year?" - $year":""), strtotime("$year-$mon-1"))."</option>";
		}
		return $month;
	}
	function lastDay($date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		return date("d", strtotime("-1 second", strtotime("+1 month", strtotime( date("Y", $time) . date("m", $time) . "01" ))));
	}
	function dateEqual($date1, $date2=''){
		//if($date2==''){$date2 = date("Y-m-d",time());}
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		if($time1==$time2){
			return true;
		}
		return false;
	}
	function addDate($date1, $date2=''){
		if($date2==''){$date2 = date("Y-m-d",time());}
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		$timediff = 0;
		if($time1>$time2){
			$timediff = $time1+$time1-$time2;
		} else{		
			$timediff = $time2+$time2-$time1;
		}
		return date("Y-m-d",$timediff);
	}
	function subDate($date1, $date2=''){
		if($date2==''){$date2 = date("Y-m-d",time());}
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		$timediff = 0;
		if($time1<$time2){
			$timediff = $time1+$time1-$time2;
		} else{		
			$timediff = $time2<$time2-$time1;
		}
		return date("Y-m-d",$timediff);
	}
	function curDay($date=''){
		return addDay(0, $date);
	}
	function nextDay($date=''){
		return addDay(1, $date);
	}
	function prevDay($date=''){
		return subDay(1, $date);
	}
	function curMonth($date=''){
		return date("m", time());
	}
	function nextMonth($date=''){
		return addMonth(1, $date);
	}
	function prevMonth($date=''){
		return subMonth(1, $date);
	}
	function curYear($date=''){
		return date("Y", time());
	}
	function nextYear($date=''){
		return addYear(1, $date);
	}
	function prevYear($date=''){
		return subYear(1, $date);
	}
	function addMonth($month, $date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		$cday = date("d",$time);
		$cmon = date("m",$time);
		$cyear = date("Y",$time);
		$months = ($cyear * 12) + $cmon + $month;
		$month = $months % 12;
		$year = ($months-$month) / 12;
		return date("Y-m-d",strtotime("$year-$month-$cday"));
	}
	function subMonth($month, $date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		$cday = date("d",$time);
		$cmon = date("m",$time);
		$cyear = date("Y",$time);
		$months = ($cyear * 12) + $cmon - $month;
		$month = $months % 12;
		$year = ($months-$month) / 12;
		return date("Y-m-d",strtotime("$year-$month-$cday"));
	}
	function addHour($hours, $time=''){
		if($time==''){$time = date("h:i:s",time());}
		$time = strtotime($time);
		return date("h:i:s",$time + ($hours*60*60));
	}
	function addDay($day, $date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		return date("Y-m-d",$time + ($day*24*60*60));
	}
	function subDay($day, $date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		return date("Y-m-d",$time - ($day*24*60*60));
	}
	function addYear($year, $date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		$year = date("Y",$time) + $year;
		return date($year."-m-d",$time);
	}
	function subYear($year, $date=''){
		if($date==''){$date = date("Y-m-d",time());}
		$time = strtotime($date);
		$year = date("Y",$time) - $year;
		return date($year."-m-d",$time);
	}
	function createEventLog($event, $etype, $details, $user, $script, $url=''){
        insert("event_log", "event, etype, details, user, date, script, url", 
		      "'$event', '$etype', '$details', '$user', NOW(), '$script', '$url'");
    }
	function letterhead($title1, $title2, $width = "100%", $logo = ""){
		$head = "<table class='print' align='center' width='$width'>";
		if($logo != ""){
			$head .= "<tr><td colspan='3' align='center'><img src='images/{$logo}' height='80px'/></td></tr>";
		}
		$head .= "<tr><td width='150px'><img src='images/logo.png' /></td><td align='center' class='btm'><h2>$title1</h2><h4>$title2</h4></td><td width='150px'></td></tr>
<tr><td colspan='3'><div style='width:100%; height:4px; background:#333;'></div></td></tr>
</table>";
		return $head;
	}
	function isonline(){
		if (!$sock = @fsockopen('www.google.com', 80, $num, $error, 5)){
			return true;
		}
		return false;
	}
	function pagetitle(){
		if(isset($_GET['q'])){
		  if($_GET['q']=='view'){
		  	echo mktitle($_GET['view']);
		  }  else{
			  $option = isset($_GET['f'])?$_GET['f']:"";
				$ptitle = mysqli_fetch_object(select("IF(COUNT(title)=0, '', title) as title", "sys_privilege", "link='{$_GET['q']}' AND `option`='$option'"));
				if($ptitle->title!=""){
				  echo $ptitle->title;
				} else{
					echo "&nbsp;";
				  //echo $_SERVER['QUERY_STRING'];
				}
			}
		}else {echo "&nbsp;";}
	}
	function build_form($fid){
		$form = select("*", "form","form_id=$fid AND active=1");
		if($form->num_rows) {
			$form = mysqli_fetch_object($form);
			$fields = select("*", "form_field", "form_id=$form->form_id AND active=1", "ORDER BY position");
			if($fields->num_rows) {
				echo "<form action='d' method='post' id='form'>
					<input type='hidden' id='type' name='type' value='$form->table' />
					<input type='hidden' id='action' name='action' value='".strtolower($form->action)."' />
					<input type='hidden' id='url' name='url' value='{$_GET['q']}' />";
				echo "<table class='form'>";
				echo "<tr><th class='header' colspan='2'>$form->form_title</th></tr>";
				while($field = mysqli_fetch_object($fields)){
					echo "<tr><td class='rht'>$field->field_title</td><td class='lft'>";
					switch(strtoupper($field->field_type)){
						case "TEXT": {
							$class = 'undefined';
							echo "<input type='text' name='$field->field_name' id='$field->field_name' size='' value='' ";
							$field->required ? $class="required" : $class="";
							echo "class='$class $field->class' />";
						} break;
						case "COMBO BOX": {
							$a_table = array();
							$class = 'undefined';
							echo "<select name='$field->field_name' id='$field->field_name' ";
							$field->required ? $class="required" : $class="";
							echo "class='$class $field->class' >";
							if($field->value_from=="Table"){
								$tables = explode("!:", $field->value);
								foreach($tables as $table){
									$table = explode(";", $table);
									$a_table[$table[0]] = $table;
								}
								$options = explode(";", $tables[0]);
								echo selectOption($options[0],$options[1],$options[2],$options[3],$options[4]);
							}elseif($field->value_from=="List"){
								$options = explode(";", $field->value);
								echo selectOption($options[0],$options[1],$options[2],$options[3],$options[4]);
							}else{
								$values = explode(",", $field->value);
								foreach($values as $value){
									echo "<option>$value</option>";
								}
							}
							echo "</select>";
							if(nn($field->relies_on)){
								?>
                                <script type="text/javascript">
									$(".<?php echo $field->relies_on; ?>").click(function(){
										$.cookie("option", this.value);
										alert("<?php echo $_COOKIE['option']; setcookie("option", "", time()-1); ?>");
										//aselect("{$field->field_name}", ""+this.value+"", ""+this.value+"", ""+this.value+"", ""+this.value+"", ""+this.value+"");
									});
								</script>
                                <?php
								//$(\"#{$field->field_name}\").html(\"\");
								//js("\$(\"#.$field->relies_on\").click(function() { alert('Handler for .click() called.'); });");
								//js("$('.$field->relies_on').click(function {alert(0);});");
								//js("$('.$field->field_name').click(function {alert(0);});");
							}
							//print_r($a_table);
						} break;
						case "RADIO BUTTON": {
							$class = 'undefined';
							if($field->value_from=="List"){
								$values = explode(",", $field->value);
							} elseif($field->value_from=="EnumField"){
								$options = explode(";", $field->value);
								echo radioEnum($field->field_name, $options[0], $options[1], $options[2]);
							}
							if(nn($field->relies_on)){
								alert($field->relies_on);
							}
							/*echo "<select name='$field->field_name'";
							$field->required ? $class="required" : $class="";
							echo "class='$class $field->class' >";
							if(nn($field->value)){
								$options = explode(";", $field->value);
								echo selectOption($options[0],$options[1],$options[2],$options[3],$options[4]);
							}if(nn($field->value)){
								$options = explode(";", $field->value);
								echo selectOption($options[0],$options[1],$options[2],$options[3],$options[4]);
							}else
								$values = explode(",", $field->value);
								foreach($values as $value){
									echo "<option>$value</option>"	
								}
							}
							echo "</select>";*/
						} break;
						case "AUTO COMPLETE":{
							
						} break;
						default:{
						
						} break;
					}
					echo "</td></tr>";
				}
				echo "<tr><td></td><td class='submission'><input type='submit' value='Submit' />&nbsp;&nbsp;<input type='reset' value='Clear All' /></td></tr>";
				echo "</table></form>";
			} else{
				echo "Form does not contain any field.<br />Click <a href='form_add_field?fid=$form->form_id'>here</a> to add Field(s).";	
			}
		} elseif(select("*", "form","form_id=$fid AND active=0")->num_rows) {
			echo "Form inactive";
		} else{
			echo "Form does not exist";
		}
		
	}
	
	function upload($files, $name, $dir = '', $_name='file'){
		$dir = $dir==''?'uploads':$dir;
		$filename = $name.ext($files[$_name]["name"]);
		//print $filename;
		if ($files[$_name]["error"] > 0){
			echo "Return Code: ".$files[$_name]["error"] . "<br />";
			$filename=false;
		}else{
			if (file_exists("$dir/$filename")){
				echo $files[$_name]["name"] . " already exists. ";
				$filename=false;
			} else {
				move_uploaded_file($files[$_name]["tmp_name"], "$dir/$filename");
				//$filename->name = $name.ext($files["file"]["name"]);
				//$filename->url = "uploads/".$name.ext($files["file"]["name"]);
				//echo "<img src='".$path.time().substr($files["file"]["name"],strlen($files["file"]["name"])-4)."' />";
			}
		}
		return $filename;
	}
	
	function ext($filename){
		$filename = trim($filename);
		$ext = '';
		$len = 2;
		while(!strpos("a".$ext, ".")){
			$ext = substr($filename,strlen($filename)-$len++);
			if($len>12){$ext=''; break;}
		}
		return $ext;
	}
	/*function upload($files, $name='', $prefix='', $path = 'files'){
		$filename;
		//id($table, $field, $length, $alphanumeric = false, $prefix = "", $suffix = "")
		if($name=='') {$name=id(0, $prefix);}
		if ($files["file"]["error"] > 0){
			echo "Return Code: ".$files["file"]["error"] . "<br />";
			$filename=false;
		}else{
			if (file_exists($path.$name.ext($files["file"]["name"]))){
				echo $files["file"]["name"] . " already exists. ";
				$filename=false;
			} else {
				move_uploaded_file($files["file"]["tmp_name"], $path.$name.ext($files["file"]["name"]));
				$filename->name = $name.ext($files["file"]["name"]);
				$filename->url = $path.$name.ext($files["file"]["name"]);
				//echo "<img src='".$path.time().substr($files["file"]["name"],strlen($files["file"]["name"])-4)."' />";
			}
		}
		return $filename;
	}*/
	function reg($var, $val){
		global $registry;
		if(!$val){
			if(isset($registry[$var])){
				$value = $registry[$var];
				unset($registry[$var]);
				return $value;
			} else {
				return 0;	
			}
		}
		if(!isset($registry[$var])){
			$registry[$var]=$val;
		} else{
			$registry[$var]+=$val;
		}
	}
	function includeifexists($filename, $dir = "./pages/", $level = 0, $found = false){
		if (!$found){
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (filetype($dir."/".$file)=="dir") {
							if($file!="." && $file!=".."){
								if($file=="." || $file==".." || in_array($file, array('ajax', 'forms', 'objects', 'pss', 'print', 'view'))){
								} else{							
									$found = includeifexists($filename, $dir."/".$file, $level+1, $found);
								}
							}
						}
						else {
							if($file==$filename){
								include($dir."/".$file);
								$found = true;
								return $found;
							}
						}
					}
				closedir($dh);
				}
			}
		}
		return $found;
	}
	
	function filelist($dir="."){
		$files = array();
		$folders = array();
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($dir."/".$file)=="dir") {
					if($file!="." && $file!=".."){
						if($file=="." || $file==".."){
						} else{							
							array_push($folders, $file);
						}
					}
				}
				else {
					if($file!="." && $file!=".."){
						array_push($files, $file);						
					}
				}
			}
		closedir($dh);
		}
		return array($files, $folders);
	}
	function folderlist($dir=".", $folders){
		//$folders = array();
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($dir."/".$file)=="dir") {
					if($file!="." && $file!=".."){
						if($file=="." || $file==".."){
						} else{							
							array_push($folders, $dir."/".$file);
							$folders = folderlist($dir."/".$file, $folders);
						}
					}
				}
			}
		closedir($dh);
		}
		return $folders;
	}
	function sum($index, $val=false){
		global $sum;
		if(!$val){
			if(isset($sum[$index])){
				return $sum[$index];
			} else {
				return 0;	
			}
		}
		if(!isset($sum[$index])){
			$sum[$index]=$val;
		} else{
			$sum[$index]+=$val;
		}
	}
	function counta($index, $get=false){
		global $counta;
		if($get){
			if(isset($counta[$index])){
				return $counta[$index];
			} else {
				return 0;	
			}
		}
		if(!isset($counta[$index])){
			$counta[$index]=1;
		} else{
			$counta[$index]++;
		}
	}
	function pin(){
		global $c;
		$uniquepin = false;
		$pin = rand(1,9).rand(0,9).substr(time(),4,6).rand(0,9).rand(0,9);
		$tries = 0;
		while(!$uniquepin){
			$tries++;
			$q = $c->query("SELECT COUNT(*) as isexist FROM card WHERE pin={$pin}");
			$p = mysqli_fetch_object($q);
			if ($p->isexist==0){
				$uniquepin = true;
				return $pin;
			}
		}
	}
	function zerofill($num, $digit){
		$zeros = $digit - strlen($num);
		for($i=0;$i<$zeros;$i++){
			$num = "0".$num;
		}
		return $num;
	}
	function js($js){
		echo "<script type='text/javascript'>$js</script>";
	}
	function cjsf($func){ //call javascript function
		js("$func();");
	}
	function mktitle($name){
		return ucwords(str_replace(array("_")," ",$name));
	}
	//===============================
	function secchk(){
		if(!isset($indexloaded)){die('');}
	}
	function operator($code){
		$code = substr($code,0,3);
		$qr = mysqli_fetch_object(select("id", "operator", "code='{$code}'"));
		return $qr->id; 
	}
	function enumVals($table,$field,$sorted=true,$upper=false){
		global $c;
		$result=$c->query('SHOW COLUMNS FROM '.$table);
		$types = array();
		while($tuple=mysqli_fetch_object($result)){
			if($tuple->Field == $field){				
				$types=$tuple->Type;
				$beginStr=strpos($types,"(")+1;
				$endStr=strpos($types,")");
				$types=substr($types,$beginStr,$endStr-$beginStr);				
				$types=str_replace("'","",$types);
				$types=explode(',',$types);
				if($sorted)
					sort($types);
				break;
			}
		}
		return $types;
	}
	function selectEnum($attribute, $table, $field, $select="", $only=array(), $sorted=true, $upper=false, $optional=false){
		$types = enumVals($table,$field,$sorted,$upper);
		$options = "";
		if($optional){
			$options = "<option></option>";
		}
		foreach($types as $type){
			if(count($only)>0 && !in_array($type, $only)) continue;
			$options .= "<option";
			if($select==$type) $options .= " selected='selected' ";
			$options .= ">".$type."</option>"; 
		}
		return "<select $attribute>".$options."</select>";
	}
	function selectEnum2($table, $field, $select="", $sorted=true, $upper=false, $optional=false){
		$types = enumVals($table,$field,$sorted,$upper);
		$options = "";
		if($optional){
			$options = "<option></option>";
		}
		foreach($types as $type){
			$options .= "<option";
			if($select==$type) $options .= " selected='selected' ";
			$options .= ">".$type."</option>"; 
		}
		return $options;
	}
	function radioEnum($name,$table,$field,$select,$sorted=true,$upper=false){
		$types = enumVals($table,$field,$sorted,$upper);
		$options = "";
		foreach($types as $type){
			$options .= "<input type='radio' value='$type' name='$name' class='$name' ";
			if($type==$select){
				$options .= " checked='checked' ";	
			}
			$options .= " />".$type."<br />"; 
		}
		return $options;
	}
	function selectOption($attribute, $table, $dataField, $valueField = "", $select = "", $filter = "", $extra = "", $optional = false){
		if($valueField=="") {$valueField=$dataField;}
		$values = select("$valueField as value, $dataField as data", $table, $filter, $extra);
		$options = "";
		if($optional){
			$options = "<option></option>";
		}
		if($values)
		while($option = mysqli_fetch_object($values)){
			$options .= "<option value='".$option->value."'";
			if(isset($select)){
				if(is_array($select)){
					if(in_array(trim($option->value), $select) || in_array(trim($option->data), $select)){
						$options .= " selected='selected' ";
					}
				} elseif($select != ""){
					if(trim($option->value)==trim($select) || trim($option->data)==trim($select)){
						$options .= " selected='selected' ";	
						unset($select);
					}
				}
			}
			$options .= ">".$option->data."</option>"; 
			
		}
		//if ($new){
			//$options .= "<option onclick=\"newEntry('$table','$dataField')\">* NEW *</option>";
		//}
		return "<select $attribute>".$options."</select>";
	}
	function tableFields($table){
		global $c;
		$arr_field = array();
		$fields=$c->query('SHOW COLUMNS FROM '.$table);
		while($field=mysqli_fetch_object($fields)){	
			array_push($arr_field,$field->Field);
		}
		return $arr_field;
	}
	function tabulate($heading, $data, $fields, $footer, $options = array()){
		$con = "<table align='center' class='tablesorter'>";
		$con .= "<thead><tr>";
		if(in_array('sl', $options)){ $con .= "<th>Sl.</th>"; }
		foreach($heading as $h){ $con .= "<th>$h</th>"; }
		$con .= "</tr></thead>";
		$con .= "<tbody>";
		foreach($footer as $ft){
			eval("$".$ft[1]."=0;"); 
		}
		$i = 1;
		while($row = mysqli_fetch_array($data)){
			$con .= "<tr>";
			if(in_array('sl', $options)){ $con .= "<td>$i</td>"; }
			foreach($fields as $f){
				if(is_array($f)){
					//eval('$con .= "<td>".$f[1]($row[$f[0]])."</td>";');
					$con .= "<td>".call_user_func($f[1],$row[$f[0]])."</td>";
				} else{
					$con .= "<td>".$row[$f]."</td>";
				}
			}
			$con .= "</tr>";
			foreach($footer as $ft){
				eval($ft[0].";");
			}
			$i++;
		}
		$con .= "</tbody>";
		$con .= "<tfoot><tr>";
		$i = 1;
		foreach($heading as $h){ 
			$con.= "<th>";
			foreach($footer as $ft){
				$key = key($footer);
				if($key==$i){
					//$con .= $$ft[1];	
					$con .= $$footer[$key][1];	
				}
				next($footer);		
			}
			$con .= "</th>"; 
			$i++;
		}
		$con .= "</tr></tfoot>";
		$con .= "</table>";
		return $con;
	}
	function view($fields, $table, $filter = "", $options = "", $extra = "", $cal = ""){
		$acl = select($fields, $table, $filter, $options);
		$fields = array();
		$maps = array();
		$field_count = $acl->field_count;
		$link = "";
		$linkfield = "";
		$linkto = "";
		$viewid = strip($table, ",");
		echo "<table class='grid' id='view-table-$viewid[0]' align='center'>";
		while($f = mysqli_fetch_field($acl)){
			array_push($fields, $f->name);
		}
		for($i=0; $i<$field_count; $i++){
			/*$field_maps = select("*", "field_map", "table = '$table' AND field = '{$fields[$i]}'");
			if($field_maps->num_rows > 0){
				$field_map = mysqli_fetch_object($field_maps);
				if($field_map->field == $fields[$i]){
					if(nn($field_map->link)){
						$link = $field_map->link;
						$linkfield = $field_map->field;
						$linkto = $field_map->linkfield;
					}
					array_push($maps, $field_map->alias);
				}
			} else{*/
				array_push($maps, mktitle($fields[$i]));
			//}
		}
		echo "<tr class='tableheading'><th>No.</th>";
		for($i=0; $i<$field_count; $i++){
			echo "<th class='th$fields[$i]'>".$maps[$i]."</th>";
		}
		echo "</tr>";
		$count = 1;
		while($a = mysqli_fetch_object($acl)){
			echo "<tr><td>$count</td>";
			for($i=0; $i<$field_count; $i++){
				$td = "<td>".$a->$fields[$i]."</td>";
				if(nn($link)){
					if($linkfield==$fields[$i]){
						echo "<td class='$fields[$i]'><a href='$link"."=".$a->$linkto."'>".$a->$fields[$i]."</a></td>";
					}
				}
				echo $td;
			}
			echo $extra;
			echo "</tr>";
			$count++;
		}
		echo "</table>";	
	}
	class vForm{
		function vForm($field, $val, $url, $heading){
			$this->$field = $field;
			$this->$val = $val;
			$this->$url = $url;
			$this->$heading = $heading;
		}
		function build(){
			return "<form action=\'d\' method=\'post\'>
				<input type=\'hidden\' name=\'type\' value=\'holiday\' />
				<input type=\'hidden\' name=\'action\' value=\'update\' />
				<input type=\'hidden\' name=\'stat\' value=\'2\' />
				<input type=\'hidden\' name=\'id\' value=\'', h.id, '\' />
				<input type=\'hidden\' name=\'url\' value=\'holiday\' />
				<input type=\'submit\' value=\'Approve\' /></form>";
		}
	}
	function view2($fields, $table, $filter = "", $options = "", $extra = null){
		if($extra){print_r($extra);}
		/*$acl = select($fields, $table, $filter, $options);
		$fields = array();
		$maps = array();
		$field_count = $acl->field_count;
		$link = "";
		$linkfield = "";
		$linkto = "";
		$viewid = strip($table, ",");
		echo "<table class='grid' id='view-table-$viewid[0]' align='center'>";
		while($f = mysqli_fetch_field($acl)){
			array_push($fields, $f->name);
		}
		for($i=0; $i<$field_count; $i++){
			$field_maps = select("*", "field_map", "table = '$table' AND field = '{$fields[$i]}'");
			if($field_maps->num_rows > 0){
				$field_map = mysqli_fetch_object($field_maps);
				if($field_map->field == $fields[$i]){
					if(nn($field_map->link)){
						$link = $field_map->link;
						$linkfield = $field_map->field;
						$linkto = $field_map->linkfield;
					}
					array_push($maps, $field_map->alias);
				}j
			} else{
				array_push($maps, mktitle($fields[$i]));
			}
		}
		echo "<tr class='tableheading'>";
		for($i=0; $i<$field_count; $i++){
			echo "<th class='th$fields[$i]'>".$maps[$i]."</th>";
		}
		echo "</tr>";
		while($a = mysqli_fetch_object($acl)){
			echo "<tr>";
			for($i=0; $i<$field_count; $i++){
				$td = "<td class='$fields[$i]'>".$a->$fields[$i]."</td>";
				if(nn($link)){
					if($linkfield==$fields[$i]){
						echo "<td class='$fields[$i]'><a href='$link"."=".$a->$linkto."'>".$a->$fields[$i]."</a></td>";
					}
				}
				echo $td;
			}
			echo $extra;
			echo "</tr>";
		}
		echo "</table>";*/	
	}
	function getxml($fields, $table, $filter = "", $options = "", $extra = ""){
		$acl = select($fields, $table, $filter, $options);
		$fields = array();
		$maps = array();
		$field_count = $acl->field_count;
		$link = "";
		$linkfield = "";
		$linkto = "";
		$viewid = strip($table, ",");
		echo "<$table>";
		while($f = mysqli_fetch_field($acl)){
			array_push($fields, $f->name);
		}
		for($i=0; $i<$field_count; $i++){
			$field_maps = select("*", "field_map", "table = '$table' AND field = '{$fields[$i]}'");
			if($field_maps->num_rows > 0){
				$field_map = mysqli_fetch_object($field_maps);
				if($field_map->field == $fields[$i]){
					if(nn($field_map->link)){
						$link = $field_map->link;
						$linkfield = $field_map->field;
						$linkto = $field_map->linkfield;
					}
					array_push($maps, $field_map->alias);
				}
			} else{
				array_push($maps, ucfirst($fields[$i]));
			}
		}
		//echo "<tr class='tableheading'>";
		//for($i=0; $i<$field_count; $i++){
			//echo "<th class='th$fields[$i]'>".$maps[$i]."</th>";
		//}
		//echo "</tr>";
		while($a = mysqli_fetch_object($acl)){
			echo "<tr>";
			for($i=0; $i<$field_count; $i++){
				$td = "<td class='$fields[$i]'>".$a->$fields[$i]."</td>";
				if(nn($link)){
					if($linkfield==$fields[$i]){
						echo "<td class='$fields[$i]'><a href='$link"."=".$a->$linkto."'>".$a->$fields[$i]."</a></td>";
					}
				}
				echo $td;
			}
			echo $extra;
			echo "</tr>";
		}
		echo "</$table>";	
	}
	function getFieldValue($tables, $field, $criteria="", $options=""){
		$recordset = select($field, $tables, $criteria, $options);
		$record = mysqli_fetch_object($recordset);
		if($record) {$fields = explode(".", $field); if(count($fields)>1) {return $record->$fields[1];} else{return $record->$fields[0];}} else {return '';}
	}
	function select($fields, $tables="", $criteria="", $options=""){
		global $c;
		if($tables==""){
			$query = $fields;
		} else{
			$query = "SELECT ".$fields." FROM ".$tables;
			if($criteria!=""){
				$query.=" WHERE ".$criteria;
			}
			$query.=" ".$options;
		}
		//print($query);
		//print("<option>".$query."</option>");
		return $c->query($query);
	}
	function deleteAllFiles($dir){
		foreach(glob($dir.'*.*') as $v){
    		unlink($v);
		}	
	}
	function makeSelectOption($attr, $options = array(), $select = 0, $zerofill = false){
		$option = "<select $attr>";
		foreach($options as $o){
			$option .= "<option ";
			if($o==$select) {$option .= "selected='selected'";}
			$option .= ">$o</option>";
		}
		$option .= "</select>";
		return $option;
	}
	function createSelectOption($attr, $start, $end, $select = 0, $zerofill = false){
		$option = "<select $attr>";
		for($i=$start; $i<=$end; $i++){
			$option .= "<option ";
			if($i==$select) {$option .= "selected='selected'";}
			$option .= ">";
			if($zerofill) {$option .= zerofill($i, $zerofill);} else {$option .= $i;}
			$option .= "</option>";
		}
		$option .= "</select>";
		return $option;
	}
	function createSelectOption2($attr, $start, $end, $options = array()){
		$option = "<select $attr>";
		for($i=$start; $i<=$end; $i++){
			$option .= "<option ";
			if(isset($options['select'])) {if($i==$options['select']) {$option .= "selected='selected'";}}
			$option .= " value='$i'>".(isset($options['ordinal'])?ordinal($i):$i)."</option>";
		}
		$option .= "</select>";
		return $option;
	}
	function selectp($fields, $tables="", $criteria="", $options=""){
		global $c;
		if($tables==""){
			$query = $fields;
		} else{
			$query = "SELECT ".$fields." FROM ".$tables;
			if($criteria!=""){
				$query.=" WHERE ".$criteria;
			}
			$query.=" ".$options;
		}
		print(htmlentities($query));
		//print("<option>".$query."</option>");
		return $c->query($query);
	}
	function mfo($result, $error_reporting = true){
		if($result){
			if($result->num_rows){
				return mysqli_fetch_object($result);
			} else{
				if($error_reporting){
					echo 'No records found!'; return false;
				} else{
					return false;
				}
			}
		} else{
			if($error_reporting){
				echo 'There is an error with the request!'; return false;
			} else{
				return false;
			}
		}
	}
	function mfos($fields, $tables="", $criteria="", $options=""){
		return mfo(select($fields, $tables, $criteria, $options));
	}
	function update($tables, $values, $criteria=""){
		global $c;
		$query = "UPDATE ".$tables." SET ".$values;
		if($criteria!=""){
			$query.=" WHERE ".$criteria;
		}
		//print($query);
		return $c->query($query);
	}
	function updatep($tables, $values, $criteria=""){
		global $c;
		$query = "UPDATE ".$tables." SET ".$values;
		if($criteria!=""){
			$query.=" WHERE ".$criteria;
		}
		print($query);
		return $c->query($query);
	}
	function del($tables, $criteria=""){
		global $c;
		$query = "DELETE FROM ".$tables;
		if($criteria!=""){
			$query.=" WHERE ".$criteria;
		}
		//print($query);
		msg("The ".ucfirst(str_replace("_", " ", $tables))." has been successfully deleted!", true);
		return $c->query($query);
	}
	function delp($tables, $criteria=""){
		global $c;
		$query = "DELETE FROM ".$tables;
		if($criteria!=""){
			$query.=" WHERE ".$criteria;
		}
		print($query);
		msg("The ".ucfirst(str_replace("_", " ", $tables))." has been successfully deleted!", true);
		return $c->query($query);
	}
	function insert($table, $fields = "", $values, $storeinevent = false){
		global $c;
		if($fields!=""){
			$fields = " (".$fields.")";
		}
		$query = "INSERT INTO ".$table.$fields." VALUES(".$values.")";
		//print($query)."<br />";
		$c->query($query);
		if($storeinevent){
			event($events['di'],"Database Insert");
		}
		return $c->insert_id;
	}
	function insertp($table, $fields = "", $values, $storeinevent = false){
		global $c;
		if($fields!=""){
			$fields = " (".$fields.")";
		}
		$query = "INSERT INTO ".$table.$fields." VALUES(".$values.")";
		print($query)."<br />";
		$c->query($query);
		/*if($storeinevent){
			event($events['di'],"Database Insert");
		}
		return $c->insert_id;*/
	}
	function replace($table, $fields = "", $values, $storeinevent = false){
		global $c;
		if($fields!=""){
			$fields = " (".$fields.")";
		}
		$query = "REPLACE INTO ".$table.$fields." VALUES(".$values.")";
		$c->query($query);
		return $c->insert_id;
	}
	function replacep($table, $fields = "", $values, $storeinevent = false){
		global $c;
		if($fields!=""){
			$fields = " (".$fields.")";
		}
		$query = "REPLACE INTO ".$table.$fields." VALUES(".$values.")";
		print($query)."<br />";
		$c->query($query);
		if($storeinevent){
			event($events['di'],"Database Insert");
		}
		return $c->insert_id;
	}
	function exists($tables, $filters){
		$data = select("*", $tables, $filters);
		if ($data->num_rows > 0){
			return true;
		} else{
			return false;
		}
	}
	function id($table, $field, $length, $alphanumeric = false, $prefix = "", $suffix = ""){
		$uid = "";
		$unique = false;
		$first = true;
		while(!$unique){
			if($alphanumeric){
				
			} else{
				if($first){
					$uid .= rand(1,9);
					$first = false;
				}
				for($l = 2; $l<=$length; $l++){
					$uid .= rand(0,9);
				}
			}
			$uid = $prefix.$uid.$suffix;
			$unique = !isin($table, "$field = '$uid'");
			//if(!$unique) alert(0);
		}
		return $uid;
	}
	function event($type, $details){
		global $c;
		global $events;
		$type = $events[$type];
		$q = insert("event", "type,details,time", "'$type', '$details',now()", true);
	}
	function error(){
			
	}
	function getfpath($name, $ext = "php"){
		 
	}
	function strip($s,$c){
		$sa = array();
		$t = "";
		for ($i=0; $i<strlen($s); $i++){
			if($s[$i] != $c){
				$t .= $s[$i];
			} else{
				array_push($sa, $t);
				$t = "";
			}
		}
		if ($t != ""){
			array_push($sa, $t);
			$t = "";
		}
		return $sa;
	}
	//nn function checks whether the argument passed is null or not - returns true if not null, if default value passed it returns default value.
	function nn($var=null, $default = false){
		return (trim($var)!="" && $var!=null && $var!='0000-00-00' && $var!='0000-00-00 00:00:00' && $var!='00:00:00') ? ($default?$default:true) : ($default?$default:false);
	}
	function sadm(){
		global $sadm;
		return (uid() == $sadm)? true : false;
	}
	function filename($filepath = ""){
		if($filepath==""){
			$filepath = $_SERVER['SCRIPT_FILENAME'];	
		}
		foreach(strip($filepath, "/") as $r){
			if(strpos($r,".php") != false){
				return $r;	
			}
		}
	}
	function msg($message = "", $append = false){
		if($message == ""){
			if(isset($_COOKIE['msg'])){
				$msg = $_COOKIE['msg'];
				setcookie('msg',"",time()-1);
				return '<span id="msg-text" style="background-color:#0CC; color:#ff0000; padding:2px 10px 0 10px;">'.$msg.'</span>';
			}
		} else{
			if($append){
				if(isset($_COOKIE['msg'])){
					$message = $_COOKIE['msg']."<br />".$message;
				}
			}
			setcookie("msg", $message);	
		}		
	}
	function showmsg(){
		if(isset($_COOKIE['msg'])) {
			$msg = $_COOKIE['msg'];
			setcookie('msg',"",time()-1);
			echo $msg;
		}
	}
	function setmsg($msg){
		setcookie("msg", $msg);
	}
	function dpath(){
		global $rd;
		$dir = "../".$rd;
		if(isset($_GET['q'])){
			$dir .= "/".$_GET['q'];
		}
		if(isset($_GET['s'])){
			$dir .= "/".$_GET['s'];
		}
		return $dir."/";
	}
	function fpath(){
		$fpath = dpath();
		if(isset($_GET['a'])){
			$fpath .= $_GET['a'].".php";
		}
		return $fpath;
	}
	function redir($url){
		echo "<script type='text/javascript'>location.href='{$url}';</script>";
	}
	function settitle($title){
		echo "<script type='text/javascript'>document.title += ' :: {$title}';</script>";
	}
	function alert($msg){
		echo "<script type='text/javascript'>alert('{$msg}');</script>";
	}
	function close(){
		echo "<script type='text/javascript'>window.close();</script>";
	}
	function idletime(){
		$tdr = select("TIME_TO_SEC(TIMEDIFF(CONCAT(CURDATE(),' ',CURTIME()),
							last_login)) AS t", "user", "id = '".uid()."'");	
		$td = mysqli_fetch_object($tdr);
		return $td->t;	
	}
	function checkidletime(){
		global $c;
		if(idletime() < 900000 ){
			update("user", "time = CURTIME()", "id = '".uid()."'");
		} else{
			//redir("?q=logout");	
		}
	}
	function extractNumber($str){
		$num = "";
		for($i=0; $i< strlen($str); $i++){
			if(is_numeric($str[$i])){
				$num .= $str[$i];	
			}
		}
		return $num;
	}
	function createDir($dir){
		$dirs = explode("/", $dir);
		$root = "";
		foreach($dirs as $dir){
			if(!file_exists($root.$dir)) { mkdir($root.$dir); }
			$root .= $dir."/";
		}
	}
	function nid($field, $table, $length = false, $prefix=""){
		$is = select($field, $table, "", "ORDER BY $field DESC LIMIT 1");
		$id = 1;
		if($is->num_rows) { $in = mysqli_fetch_object($is); $id = $in->$field + 1; }
		return $prefix.zerofill($id, $length);
	}
	function space($times){
		$spaces = "";
		for($i=0; $i<=$times; $i++){
			$spaces .= "&nbsp;";	
		}
		return $spaces;
	}
	//===================== TO NUMBER       
	$nwords = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen", "twenty", 30 => "thirty", 40 => "forty", 50 => "fifty", 60 => "sixty", 70 => "seventy", 80 => "eighty", 90 => "ninety" ); 
function num2txt($x, $postfix = ""){
	$nums = explode(".", $x);
	return trim(num2txt2($nums[0]).(isset($nums[1])?" and ".num2txt2($nums[1]):"")." $postfix");
}
function num2txt2($x){
	$x = number_format($x,0,"","");
   global $nwords;
   if(!is_numeric($x))
   {
	   $w = '#';
   }else if(fmod($x, 1) != 0)
   {
	   $w = '#'; 
   }else{
	   if($x < 0)
	   {
		   $w = 'minus ';
		   $x = -$x;
	   }else{
		   $w = '';
	   } 
	   if($x < 21)
	   {
		   $w .= $nwords[$x];
	   }else if($x < 100)
	   {
		   $w .= $nwords[10 * floor($x/10)];
		   $r = fmod($x, 10); 
		   if($r > 0)
		   {
			   $w .= '-'. $nwords[$r];
		   }
	   } else if($x < 1000)
	   {
		   $w .= $nwords[floor($x/100)] .' hundred'; 
		   $r = fmod($x, 100);
		   if($r > 0)
		   {
			   $w .= ' and '. num2txt($r);
		   }
	   } else if($x < 100000) 
	   {
		   $w .= num2txt(floor($x/1000)) .' thousand';
		   $r = fmod($x, 1000);
		   if($r > 0)
		   {
			   $w .= ' '; 
			   if($r < 100)
			   {
				   $w .= 'and ';
			   }
			   $w .= num2txt($r);
		   } 
	   } else {
		   $w .= num2txt(floor($x/100000)) .' lakh';
		   $r = fmod($x, 100000);
		   if($r > 0)
		   {
			   $w .= ' '; 
			   if($r < 100)
			   {
				   $word .= 'and ';
			   }
			   $w .= num2txt($r);
		   } 
	   }
   }
   return ucfirst($w);
}
function isEmail($email){
	$regex = "/\w+@\w+.\w+/";
	return preg_match($regex, $email);
}
function email($email, $subject, $body, $attachment = ''){
	$email      = $email;
	require 'lib/phpmailer/class.phpmailer.php';
	require 'lib/phpmailer/class.smtp.php';

	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$mail->IsSMTP();                                      // set mailer to use SMTP
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->SMTPSecure = "tls";
		$mail->Host = "just18.justhost.com";  // specify main and backup server
		$mail->Port = 26;
		$mail->Username = "system@agdfreshmeat.com";  // SMTP username
		$mail->Password = '$ys1(m12'; // SMTP password
		
		$mail->From = "system@agdfreshmeat.com";
		$mail->FromName = "Fresh Meat";
		if(is_array($email)){
			if(count($email)>0){
				foreach($email as $to){
					$mail->AddAddress($to, $to);
				}
			}
		} elseif($email != ""){
			$mail->AddAddress($email, $email);
		}
		
		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);                                  // set email format to HTML
		
		$mail->Subject = $subject;
		$mail->Body    = $body;
		if(is_array($attachment)){
			if(count($attachment)>0){
				foreach($attachment as $attach){
					$mail->addAttachment($attach); 
				}
			}
		} elseif($attachment != ""){
			$mail->addAttachment($attachment); 
		}
		
		if(!$mail->Send())
		{
			 echo "Message could not be sent. <p>";
			 echo "Mailer Error: " . $mail->ErrorInfo;
			 exit;
		}
		
		//echo "Your verification code has been sent to $email.";
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}
}
function email2($email, $subject, $body, $attachment = ''){
	$email      = $email;
	require '../../lib/phpmailer/class.phpmailer.php';
	require '../../lib/phpmailer/class.smtp.php';

	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$mail->IsSMTP();                                      // set mailer to use SMTP
		$mail->SMTPAuth = true;     // turn on SMTP authentication
		$mail->SMTPSecure = "tls";
		$mail->Host = "just18.justhost.com";  // specify main and backup server
		$mail->Port = 26;
		$mail->Username = "system@agdits.com";  // SMTP username
		$mail->Password = '$ys1(m12'; // SMTP password
		
		$mail->From = "system@agdits.com";
		$mail->FromName = "Fresh Meat";
		if(is_array($email)){
			if(count($email)>0){
				foreach($email as $to){
					$mail->AddAddress($to, $to);
				}
			}
		} elseif($email != ""){
			$mail->AddAddress($email, $email);
		}
		
		$mail->WordWrap = 50;                                 // set word wrap to 50 characters
		$mail->IsHTML(true);                                  // set email format to HTML
		
		$mail->Subject = $subject;
		$mail->Body    = $body;
		if(is_array($attachment)){
			if(count($attachment)>0){
				foreach($attachment as $attach){
					$mail->addAttachment($attach); 
				}
			}
		} elseif($attachment != ""){
			$mail->addAttachment($attachment); 
		}
		
		if(!$mail->Send())
		{
			 echo "Message could not be sent. <p>";
			 echo "Mailer Error: " . $mail->ErrorInfo;
			 exit;
		}
		
		//echo "Your verification code has been sent to $email.";
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}
}

class Tweet
{
    public $ID = 0;
    public $Words = array();
    public $vm = 0;
    public $vmsd = 0;
    public $am = 0;
    public $amsd = 0;
    
    function __construct($ID, $Words, $vm, $vmsd, $am, $amsd)
    {
        $this->ID = $ID;
        $this->Words = $Words;
        $this->vm = $vm;
        $this->vmsd = $vmsd;
        $this->am = $am;
        $this->amsd = $amsd;
    }
}