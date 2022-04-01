<?php 
// Контроль вывода сайдбаров на закрытых страницах

function uac_admin_menu_sidebar_control()
{
	//add_submenu_page( basename(__FILE__), 'Управление доступом к сайдбарам', 'Сайдбары', 8, basename(__FILE__).'_sidebar_control', 'get_uac_sidebar_control');

}

//add_action('admin_menu', 'uac_admin_menu_sidebar_control');

function uac_get_closed_sidebars_array()
{
	$uac_sidebars_array = explode("|_|", get_option('uac_closer_sidebars'));
	return $uac_sidebars_array;
}

function uac_update_closed_sidebars_array($closed_sidebars_array)
{
	// $closed_sidebars_array - массив с id сайдбаров

	if ($closed_sidebars_array)
	{
		$closed_sidebars_option_value = implode("|_|", $closed_sidebars_array);
		echo ' ++ '.$closed_sidebars_option_value." ++";
	}
	else
	{
		$closed_sidebars_option_value = '';
	}
	update_option( 'uac_closer_sidebars',  $closed_sidebars_option_value);
}

function uac_get_closed_widgets_array()
{
	$uac_widgets_array = explode("|_|", get_option('uac_closer_widgets'));
	return $uac_widgets_array;
}

function uac_update_closed_widgets_array($closed_widgets_array)
{
	// $closed_sidebars_array - массив с id сайдбаров
	//update_option( 'uac_closer_widgets', implode("||", $closed_widgets_array) );
	echo "==";
	print_r ($closed_widgets_array);
	echo "==";
	if ($closed_widgets_array)
	{
		$closed_widgets_option_value = implode("|_|", $closed_widgets_array);
	}
	else
	{
		$closed_widgets_option_value = '';
	}
	update_option( 'uac_closer_widgets',  $closed_widgets_option_value);
}

function get_uac_sidebar_control()
{
	global $wp_registered_widgets, $wpdb;
	$uac_closed_sidebars = (isset($_POST['uac_closed_sidebars'])) ? $_POST['uac_closed_sidebars'] : '';
	echo "--";
	print_r ($_POST['uac_closed_sidebars']);
	echo "--";
	if (isset($_POST['uac_sidebars_save'])) //Если нажата кнопка Сохранить
    {
		//uac_closer_sidebars get_option('uac_active')
		//implode(",", $array)
		uac_update_closed_sidebars_array($uac_closed_sidebars);
		//update_option( 'uac_closer_sidebars', implode("||", $uac_closed_sidebars) );

	}


	echo "--";
	print_r ($_POST['uac_closed_widgets']);
	echo "--";
	echo $_POST['uac_widgets_save'];
	echo "--";
	$uac_closed_widgets = (isset($_POST['uac_closed_widgets'])) ? $_POST['uac_closed_widgets'] : '';
	if (isset($_POST['uac_widgets_save'])) //Если нажата кнопка Сохранить
    {
		//uac_closer_sidebars get_option('uac_active')
		//implode(",", $array)
		uac_update_closed_widgets_array($uac_closed_widgets);
		//update_option( 'uac_closer_sidebars', implode("||", $uac_closed_sidebars) );

	}
	$uac_sidebars_array = uac_get_closed_sidebars_array();
	$uac_widgets_array = uac_get_closed_widgets_array();
	//echo get_option('uac_closer_sidebars');
	//print_r ($wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'widget_%'"));
	?>
	</br>
	<?
	//print_r($GLOBALS['wp_registered_widgets']);//['pages-1']  $GLOBALS['wp_registered_widgets']['pages-1']['callback'][0]->option_name
	?>
	</br>
	</br>
	<?

	//print_r($sdfsdf[2]['title'] );//[2]['title']
	?>
	<h3>Ограничение доступа к сайдбарам</h3>
	</br>
	<form method="POST" action="">
	<table border="1" style="border-style: solid" align="center">
	<tr>
	<td><b>Сайдбар</b></td>
	<td><b>Доступ закрыт</b></td>
	</tr>
	<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { ?>
		<tr>
			<td>
				<?php echo ucwords( $sidebar['name'] ); ?>
			</td>
			<td>
				<input type='checkbox' name='uac_closed_sidebars[]' value='<?php echo  $sidebar['id'] ; ?>' <?echo (in_array($sidebar['id'], $uac_sidebars_array))? 'checked' : '';?> />
			</td>
		</td>

		 </tr>
	<?php } ?>
	</table>
	<input type="submit" name="uac_sidebars_save" value="Сохранить">
	</form>

	</br>
	</br>
	<!--
	<h3>Ограничение доступа к виджетам</h3>
	</br>
	<form method="POST" action="">
	<table border="1" style="border-style: solid" align="center">
	<tr>
	<td><b>Виджет</b></td>
	<td><b>Заголовок</b></td>
	<td><b>id</b></td>
	<td><b>Доступ закрыт</b></td>
	</tr>
	<?php /* foreach ( $GLOBALS['wp_registered_widgets'] as $widget )
	{
		$this_w_number = ($widget['params'][0]['number']);
		if ($this_w_number >1)
		{
	?>
			<tr>
				<td>
					<?php echo ucwords( $widget['name'] ); ?>
				</td>
				<td>
				<?
						$wid_option_name = $GLOBALS['wp_registered_widgets'][$widget['id']]['callback'][0]->option_name;
						$this_widget_options = get_option($wid_option_name);
						//print_r ($this_widget_options);//$this_widget_options[2]['title']
						$this_w_number = ($widget['params'][0]['number']);//$widget['callback']['params'][0]['number']
						echo $this_widget_options[$this_w_number]['title'];
						//echo "</br>";
						//echo $this_w_number;
				?>
				</td>
				<td>
					<? echo $widget['id']; ?>
				</td>
				<td>
					<input type='checkbox' name='uac_closed_widgets[]' value='<?php echo  $widget['id'] ; ?>' <?echo (in_array($widget['id'], $uac_widgets_array))? 'checked' : '';?> />
					</br>
					<? //echo $widget['id']; ?>
				</td>
			</tr>


	<?php
		}
	} ?>
	</table>
	<input type="submit" name="uac_widgets_save" value="Сохранить">
	</form> -->
<?*/
}

//Скрываем сайдбар для пользователей без доступа

function uac_remove_some_sidebars(){
	$current_role = uac_get_current_user_role();
	$is_super_client = false;
	$is_administrator = false;
	if($current_role == 'super_client') $is_super_client = true;
	if ($current_role == 'administrator') $is_administrator = true;
	if ($is_super_client || $is_administrator)
	{
		//echo "показать виджет, лог:".is_user_logged_in(). ' суперклиент: '.$is_super_client.' админ:'.$is_administrator ;
		//unregister_sidebar( 'sidebar-1' );
	}
	else
	{
		$uac_sidebars_array = uac_get_closed_sidebars_array();
		foreach ( $uac_sidebars_array as $sidebar_id )
		{
			unregister_sidebar( $sidebar_id );
		}
		//echo "скрыть виджет, лог:".is_user_logged_in(). ' суперклиент: '.$is_super_client.' админ:'.$is_administrator ;
		//unregister_sidebar( 'sidebar-1' );
	}

}
add_action( 'widgets_init', 'uac_remove_some_sidebars', 11);

function uac_remove_some_widgets()
{
	global $wp_registered_widgets;
	$current_role = uac_get_current_user_role();
	$is_super_client = false;
	$is_administrator = false;
	if($current_role == 'super_client') $is_super_client = true;
	if ($current_role == 'administrator') $is_administrator = true;
	if ($is_super_client || $is_administrator)
	{
		//echo "показать виджет, лог:".is_user_logged_in(). ' суперклиент: '.$is_super_client.' админ:'.$is_administrator ;
		//unregister_sidebar( 'sidebar-1' );
	}
	else
	{
		$uac_widgets_array = uac_get_closed_widgets_array();
		foreach ( $uac_widgets_array as $widget_id )
		{
			//echo $widget_id;
			wp_unregister_sidebar_widget($widget_id);

			//wp_unregister_widget_control( $widget_id );
		}
		//echo "скрыть виджет, лог:".is_user_logged_in(). ' суперклиент: '.$is_super_client.' админ:'.$is_administrator ;
		//unregister_sidebar( 'sidebar-1' );
	}

}
add_action( 'widgets_init', 'uac_remove_some_widgets' );
?>
