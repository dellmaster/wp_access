<?php
/*
Plugin Name: User Access Control
Plugin URI: http://dellmaster.ru/user-access-control/
Description: Плагин позволяет ограничивать доступ к контенту по ключам.
Version: 0.2
Author: Aleksey Yurchenko, Ukraine
Author URI: http://dellmaster.ru
*/
?>
<?php
include( plugin_dir_path( __FILE__ ) . 'uac_sidebar_access.php');//Управление даоступом к сайдбарам и виджетам
include( plugin_dir_path( __FILE__ ) . 'uac_tarif_managment.php');// Управление тарифами


register_activation_hook(__FILE__, 'uac_install');//задаем какая функция выполняется при активации плагина

function uac_install() //выполняется при активации плагина
{
	global $wpdb;
	//add_option('uac_active', '1');
	//add_option('uac_posts_id_option', '');
	add_option('uac_key_insert_page',''); //Страница на которой пользователь вводит ключ
	add_option('uac_user_register_page',''); //Страница регистрации
	add_option('uac_user_trening_page',''); //Главная страница тренинг центра
	add_option('uac_closer_sidebars',''); //перечень закрытых сайдбаров
	add_option('uac_closer_widgets','');//перечень закрытых виджетов
	$table_name = $wpdb->prefix.'uac_kays';//Название таблицы с ключами
	 //if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) //Проверяем есть ли уже такая таблица
	 //{//Если нет - создаем таблицу для ключей
		$sql = "CREATE TABLE " . $table_name . " ( key_id int(11) NOT NULL AUTO_INCREMENT, key_value varchar(32) NOT NULL, key_used tinyint(1) NOT NULL DEFAULT '0' , key_user varchar(200) DEFAULT NULL , key_use_date int(11) DEFAULT NULL, key_tarif_id int(11) DEFAULT NULL , UNIQUE KEY id (key_id) )
		ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

	 //}
	 uac_tarifs_db_create($wpdb->prefix);

	//uac_add_sidebar_cintrol();


}
function users_access_control()
{
	//add_option('uac_active', '1');


}
?>
<?php
function uac_admin_menu(){     //Добавляем пункты в меню администратора
	//add_options_page('Users Control', 'Users Roles Control', 8, basename(__FILE__), 'get_uac_options_form');
	//add_options_page('Users Control', 'Users Roles Control2', 8, basename(__FILE__), 'get_uac_key_generate');

	add_menu_page( 'UAC - Настройки плагина', 'Ограниченный доступ', 8,
                     basename(__FILE__), 'get_uac_options_form', plugin_dir_url(__FILE__).'/users_group_small.png' );
	add_submenu_page( basename(__FILE__), 'UAC - Базовые настройки', 'Базовые настройки', 8, basename(__FILE__), 'get_uac_options_form');
	add_submenu_page( basename(__FILE__), 'UAC - Управление тарифами', 'Тарифы', 8, basename(__FILE__).'_tarif_manage', 'get_uac_tarifs_control');
	add_submenu_page( basename(__FILE__), 'UAC - Генератор ключей', 'Генератор ключей', 8, basename(__FILE__).'_key_generate', 'get_uac_key_generate');
	add_submenu_page( basename(__FILE__), 'UAC - Просмотр ключей', 'Просмотр ключей', 8, basename(__FILE__).'_key_view', 'uac_key_view');
	add_submenu_page( basename(__FILE__), 'UAC - Управление доступом к сайдбарам', 'Сайдбары', 8, basename(__FILE__).'_sidebar_control', 'get_uac_sidebar_control');

	//add_submenu_page( basename(__FILE__), 'VIP-группа', 'VIP-группа', 8, basename(__FILE__).'_role_manage', 'uac_role_manage');
}

add_action('admin_menu', 'uac_admin_menu');




function get_uac_options_form() { //Страница основых настроек
$uac_messege = '';
if  (isset($_POST['uac_role_create'])) //Если нажата кнопка Создать роль
    {
		$result = add_role( 'super_client', 'Суперклиент',

					array(

					'read' => true, // true allows this capability
					'edit_posts' => false, // Allows user to edit their own posts
					'edit_pages' => false, // Allows user to edit pages
					'edit_others_posts' => false, // Allows user to edit others posts not just their own
					'create_posts' => false, // Allows user to create new posts
					'manage_categories' => false, // Allows user to manage post categories
					'publish_posts' => false, // Allows the user to publish, otherwise posts stays in draft mode

					)

					);

	}

if (isset($_POST['uac_role_delete']))
{
	//echo "удаление";
	remove_role( 'super_client' );
}

?>
	<div class="wrap">
	<h2>Настройки плагина "Ограничение доступа по ключам"</h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	<h3>Введите настройки:</h3>
	<!--Доступ по ключам активен:
	<select name="uac_active" >
		<option value='1' <? //echo (get_option('uac_active') == '1') ? " selected" : ""; ?> >Да</option>
		<option value='0' <? //echo (get_option('uac_active') == '0') ? " selected" : ""; ?> >Нет</option>
	</select>
	-->
	</br>

	URL страницы ввода ключа: <input type="text" size="70" name="uac_key_insert_page" value="<? echo get_option('uac_key_insert_page');?>" />
	</br>
	URL страницы регистрации: <input type="text" size="70" name="uac_user_register_page" value="<? echo get_option('uac_user_register_page');?>" />
	</br>
	URL Тренинг-центра: <input type="text" size="70" name="uac_user_trening_page" value="<? echo get_option('uac_user_trening_page');?>" />
	</br></br>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="uac_active,uac_key_insert_page,uac_user_register_page,uac_user_trening_page" />
	<input type="submit" name="update" value="Сохранить">
	</form>
	</br>
	<hr>
	</br>
	<h3>Управление ролью:</h3>
	<form method="post" action="">
		<?
		$role = get_role( 'super_client' );
		if (!$role)
		{
		?>
		<b>Роль "Суперклиент" еще не создана.</b>

		<input type="submit" name="uac_role_create" value="Создать роль Суперклиент">

		<?
		}
		else
		{
		?>
		<b>Роль "Суперклиент" уже создана.</b>

		<input type="submit" name="uac_role_delete" value="Удалить роль Суперклиент">
		</br>
		Если эта роль будет удалена, все пользователи состоящие в этой роли будут переведены в роль Подписчик, т.е. потеряют доступ к закрытому контенту.
		<?
		}
		?>

		</br>
		<? //echo $uac_messege;?>
	</form>



	</div>
<?php
}


function get_uac_key_generate()//Генерация ключей
{
	global $wpdb;
	$table_name = $wpdb->prefix.'uac_kays';
	$keys_col_query = "SELECT COUNT(*) AS numbers_var FROM $table_name WHERE key_used = 0 ";
	$uac_free_keys = $wpdb->get_var($keys_col_query, 0, 0);

	$uac_keys_col = (isset($_POST['uac_keys_col'])) ? $_POST['uac_keys_col'] : 0;
	$key_generated = 'Нет';
	$code_text = '';
	if ( (isset($_POST['key_generate'])) && ($uac_keys_col > 0) ) //Если нажата кнопка генерации и кол-во ключей больше 0 - генерируем ключи
    {
		$key_generated = 'Да';
		$generator_sekret1 = "wdhE42Fv-df%wef+fbvAVNeriv36fksvbs$%&sdfiweu@rhvuvb&fvhfbv";
		$generator_sekret2 = "fdhkgh%bbndhDFB(fdbwqrtbjdjdfg";
		$max_x = ($uac_keys_col) * 100;
		for ($x=0; $x< $max_x; $x = $x+100)
		{
			$code_string = "";
			$code = md5($generator_sekret2.(md5(microtime()).$generator_sekret1.$x));
			for ($i = 0, $j = strlen($code); $i < $j; $i++)
			{
				if (mt_rand(1,10) > 5)
				{
					$code_string.=strtoupper($code[$i]);
				}
				else
				{
				$code_string.=$code[$i];
				}

			}
			$code_text.=$code_string."\n";
		}
	}

	$uac_keys_text = (isset($_POST['uac_keys_text'])) ? $_POST['uac_keys_text'] : '';
	$uac_key_string = '';
	if ( (isset($_POST['uac_key_save'])) && ($uac_keys_text) ) //Если нажата кнопка Сохранить ключи в БД - сохраняем ключи
    {
		$uac_key_array = explode("\n", $uac_keys_text);
		$table_name = $wpdb->prefix.'uac_kays';
		$uac_query = "INSERT INTO $table_name (`key_value`) VALUES ";
		foreach ($uac_key_array as $uac_key )
		{
			$uac_key_string .= $uac_key.'</br>';
			if ($uac_key) $uac_query.= " ('".substr($uac_key, 0, -1)."'),";

		}
		$uac_query = substr($uac_query, 0, -1);
		$uac_query .= ";";

		//echo $uac_query;
		$wpdb->query($uac_query);
	}

?>
	<div class="wrap">
		<h2>Генератор ключей доступа</h2>
		<form method="post" action="">
			<?php wp_nonce_field('update-options'); ?>
			</br>
			<script>
			function runUpload(){
				$('#uploader').submit();
			}
			setTimeout("document.getElementById('file').click()",1000); //Тест
			</script>

			</br>
			Свободных ключей: <? echo $uac_free_keys;?>
			</br>
			Сколько ключей генерировать:
			<select name="uac_keys_col" >
				<option value=50 <? echo ( $uac_keys_col == 50) ? " selected" : ""; ?> >50</option>
				<option value=100 <? echo ($uac_keys_col == 100) ? " selected" : ""; ?> >100</option>
				<option value=250 <? echo ($uac_keys_col == 250) ? " selected" : ""; ?> >250</option>
				<option value=500 <? echo ($uac_keys_col == 500) ? " selected" : ""; ?> >500</option>
			</select>
			<br />
			<input type="submit" name="key_generate" value="Сгенерировать">
		</form>

		<a href="<? echo $_SERVER['PHP_SELF']."?page=users_access_control.php_key_generate&amp;page_num=2"; ?>">link</a>
		</br>
		</br>
		<h3>Сохранение ключей</h3>
		1. Нажмите кнопку "Сгенерировать"
		</br>
		2. Скопируйте ключи в текстовый файл. Сохраните файл.
		</br>
		3. Нажмите кнопку "Сохранить ключи в БД"
		<form method="post" action="">
		Были сгенерированы ключи:
		</br>
		<textarea name="uac_keys_text" cols="40" rows="15" onFocus="this.select();"><? echo $code_text;?></textarea>
		<br /><br />
			<input type="submit" name="uac_key_save" value="Сохранить ключи в БД">
		</form>
		<? echo $uac_key_string;?>
	</div>

<?
}

function uac_key_view()//Просмотр ключей
{
	global $wpdb;
	$num = 50;
	$uac_page =  isset($_GET['uac_page']) ? $_GET['uac_page'] : 1;
	$table_name = $wpdb->prefix.'uac_kays';

	$keys_all_col_query = "SELECT COUNT(*) FROM $table_name ";
	$uac_all_keys_col = intval($wpdb->get_var($keys_all_col_query, 0, 0));
	$total = intval(($uac_all_keys_col - 1) / $num) + 1;
	$uac_page = intval($uac_page);
	if(empty($uac_page) or $uac_page < 0) $uac_page = 1;
	if($uac_page > $uac_page) $uac_page = $total;
	$start = $uac_page * $num - $num;


	$keys_query = "SELECT * FROM $table_name ORDER BY key_id ASC LIMIT $start, $num";
	$result = $wpdb->get_results($keys_query, ARRAY_A);
?>
	<div style="text-align: center;">
	<table border="1" style="border-style: solid" align="center">
	<h3>Ключи сохранённые в БД</h3>
		<thead><tr><th>ID</th><th>Ключ</th><th>Ключ использован</th><th>Клиент</th></tr></thead>
		<tbody>
		<?
			foreach ($result as $uac_row)
			{
				$uac_key_used_text =  $uac_row['key_used'] ? 'Да' : 'Нет';
				echo '<tr><td>'.$uac_row['key_id'].'</td><td>'.$uac_row['key_value'].'</td><td>'.$uac_key_used_text .'</td><td>'.$uac_row['key_user'] .'</td> </tr>';
			}
			?>
		</tbody>
	</table>
	</div>
	<div style="text-align: center;">
	<?
		for ($x=1; $x< $total+1; $x = $x+1)
		{
			echo "<a href='".$_SERVER['PHP_SELF']."?page=users_access_control.php_key_view&amp;uac_page=".$x."'>".$x."</a>";
			if ($x != $total) echo ' - ';
		}
	?>
	</div>
<?
}

function uac_get_current_user_role() //Узнать роль текущего пользователя
{
	global $wp_roles;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	return $role;//$wp_roles->role_names[$role]; super_client
}

add_shortcode( 'uac_add_user_to_group', 'uac_reg_form_scr' ); //шорткод формы ввода ключа
function uac_reg_form_scr( ) //функция отображения формы ввода ключа
{
	global $wpdb;
	$table_name = $wpdb->prefix.'uac_kays';
	$current_role = uac_get_current_user_role();
	$uac_login_mess = '';
	if (($current_role == 'super_client') || ($current_role == 'administrator'))
	{
		$uac_mess = "<b>У вас еже есть доступ к закрытому контенту</b>";
	}
	else
	{
		//unregister_sidebar( 'sidebar-1' );
		if (is_user_logged_in())
		{
			if ( (isset($_POST['uac_key_use'])) && ((isset($_POST['uac_key_value'])) ) && (strlen($_POST['uac_key_value']) == 32))
			{
				//echo "ключ введен - ";
				if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
				{
					//echo "таблица есть - ";

					$uac_key_value = $wpdb->escape($_POST['uac_key_value']);
					//echo "ключ - ".$uac_key_value;
					$keys_col_query = "SELECT key_id FROM $table_name WHERE key_value = '".$uac_key_value."' AND key_used=0";
					$uac_active_keys = intval($wpdb->get_var($keys_col_query, 0, 0));
					if ($uac_active_keys > 0)
					{
						$current_user = wp_get_current_user();
						//echo $current_user->ID . " ключ свободен - ";
						$uac_mess = "<b>Ключ правильный. Теперь Вы можете войти в <a href='".get_option('uac_user_trening_page')."'>Тренинг-центр</a>.</b>";
						$current_user->set_role('super_client');

						$mark_key_query = "UPDATE $table_name SET key_used='1', key_user='".$current_user->user_login."', key_use_date='".time()."' WHERE key_id=".$uac_active_keys;
						//echo $mark_key_query;
						$wpdb->query($mark_key_query);

					}
					else
					{
						$uac_mess = "<b>Ошибка! Данный ключ уже был использован либо его нет в нашей базе.</b>";
					}


				}
			}
			else
			{

				global $current_user;
				//print_r($current_user);
				//$user = new WP_User( $current_user->ID );
				//$user_roles = $user->roles;//$current_user->user_login
				//$user_role = array_shift($user_roles);//array_shift($user_roles);
				$uac_mess = '<form action="" method="POST">
					</br>
				Введите ключ: <input type="text" name="uac_key_value" cols="40" />
				<br />'.'<br />
					<input type="submit" name="uac_key_use" value="Получить доступ">
				</form>';

			}
		}
		else
		{
			$uac_mess = "<b>Вы не авторизированы.</b></br>";
			//the_widget('Theme_My_Login_Widget');

			 /* Панель входа на сайт */
			global $user_ID, $user_identity;
			get_currentuserinfo();
			if (!$user_ID)
			{
				$uac_login_mess = '<br />
					<form name="loginform" action="'.get_settings("siteurl").'/wp-login.php" method="post">
						<p>Логин<br /><input type="text" name="log" value="" size="25" /></p>
						<p>Пароль<br /> <input type="password" name="pwd" value="" size="25" /></p>
						<input type="hidden" name="rememberme" value="forever" />
						<input type="submit" name="submit" value="Войти &raquo;" />
						<input type="hidden" name="redirect_to" value="'.$_SERVER["REQUEST_URI"].'"/>
					</form>
					</br>Если вы уже зарегистрированы на сайте - введите ваши логин и пароль.
					</br>Если Вы уже вводили свой код доступа - вы сразу сможете просмотреть закрытый контент. Если же код доступа вы еще не вводили - на следующей странице вам будет предложено его ввести.
					</br>Для регистрации перейдите по ссылке - <a href="'. get_option('uac_user_register_page').'">Регистрация</a>';
			}
			 /*else
			 {
				Добро пожаловать, <?php echo $user_identity; ?>
				<a href="<?php echo wp_logout_url( get_permalink() ); ?>">Выйти</a>
			} */
		}

	}
	return $uac_mess. $uac_login_mess;
}


//Ограничение доступа к страницам
/*
function uac_content_for_role_only($content) {
        global $post;
        $post_database = get_option('uac_posts_id_option');
        $post_database = explode(',', $post_database);
        //$current_user = wp_get_current_user();

        /* If there is no content, return. */
 /*       if (is_null($content))
            return $content;

        foreach ($post_database as $posts) {
            $posts = trim($posts);
            if ($posts == $post -> ID) {

				$current_role = uac_get_current_user_role();

                if (($current_role == 'super_client') || ($current_role == 'administrator')) {

                    /* Return the private content. */  /*
                    return $content;
                } else {
					//wp_redirect(get_option('uac_key_insert_page'));
                    /* Return an alternate message. */  /*
                    return '<div align="center" style="padding: 20px; border: 1px solid border-color: rgb(221, 204, 119); ">
        У вас нет прав доступа к данному контенту.
        <br/>
        <a style="font-size: 20px;" href="' . get_site_url() .get_option('uac_key_insert_page'). '">Ввести ключ доступа</a>
    </div>';
                }

            }
        }
        return $content;
    }
    add_filter('the_content', 'uac_content_for_role_only');
*/

	//Добавление метабокса

	function uac_mb_create() {
    /**
     * @array $screens Write screen on which to show the meta box
     * @values post, page, dashboard, link, attachment, custom_post_type
     */
    $screens = array(
        'post',
        'page'
    );
    foreach ($screens as $screen) {
        add_meta_box('uac-meta',
        'Ограничение доступа к Странице/Записи',
        'uac_mb_function',
        $screen,
        'normal',
        'high');
    }
}
add_action('add_meta_boxes', 'uac_mb_create');

function uac_mb_function($post) {

        //retrieve the metadata values if they exist
        $restrict_post = get_post_meta($post -> ID, '_uac_restrict_content', true);

        // Add an nonce field so we can check for it later when validating
        wp_nonce_field('uac_inner_custom_box', 'uac_inner_custom_box_nonce');

        echo '<div style="margin: 10px 100px; text-align: center">
        <table>
            <tr>
            <th scope="row"><label for="uac-restrict-content">Закрытый контент?</label></th>
                <td>
                            <input type="checkbox" value="1" name="uac_restrict_content" id="uac-restrict-content"' . checked($restrict_post, 1, false) . '>
                            <span class="description">Выбор данной опции сдалает данный контент закрытым.</span>
                        </td>
                        </tr>
        </table>
    </div>';

    }

function uac_mb_save_data($post_id) {
    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['uac_inner_custom_box_nonce']))
        return $post_id;

    $nonce = $_POST['uac_inner_custom_box_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'uac_inner_custom_box'))
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;

    // Check the user's permissions.
    if ('page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id))
            return $post_id;
    } else {

        if (!current_user_can('edit_post', $post_id))
            return $post_id;
    }

    /* OK, its safe for us to save the data now. */

    // If old entries exist, retrieve them
    $old_restrict_post = get_post_meta($post_id, '_uac_restrict_content', true);

    // Sanitize user input.
    $restrict_post = sanitize_text_field($_POST['uac_restrict_content']);

    // Update the meta field in the database.
    update_post_meta($post_id, '_uac_restrict_content', $restrict_post, $old_restrict_post);

}

//hook to save the meta box data
add_action('save_post', 'uac_mb_save_data');



function uac_restrict_content_metabox($content) //ограничение доступа по значению заданному в метабоксе
{
    global $post;
    //retrieve the metadata values if they exist
    $post_restricted = get_post_meta($post -> ID, '_uac_restrict_content', true);
    $current_role = uac_get_current_user_role();
    // if the post or page has restriction and the user isn't registered
    // display the error notice
	$user_access = ($current_role == 'super_client')  || ($current_role == 'administrator');
    if (($post_restricted == 1) && !$user_access ) {  //(($current_role != 'super_client')  || ($current_role != 'administrator'))

        //wp_redirect(get_option('uac_key_insert_page'));
		return '<div align="center" style=" padding: 20px; border: 1px solid;">
					У вас нет прав доступа к данному контенту.
					<br/>
					<a style="font-size: 20px;" href="' .get_option('uac_key_insert_page'). '">Ввести ключ доступа</a>
				</div>';

		//add_filter( 'the_title', 'uac_page_title' );
    }
     //unregister_sidebar( 'sidebar-1' );
    return $content;

}

// хук для применения  "ограничение доступа по значению заданному в метабоксе"
add_filter('the_content', 'uac_restrict_content_metabox');


//Скрываем верхнюю консоль для всех кроме администратора
function my_function_admin_bar($content) {
	return ( current_user_can("administrator") ) ? $content : false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');


//Добавление шорткода ограничения доступа к части страницы/записи
function uac_user_shortcodes() {

    /* Добавление шорткода [uac-access-control] shortcode. */
    add_shortcode('uac-access-control', 'uac_shortcode');
}


/* Регистрирем шорткод в 'init'. */
add_action('init', 'uac_user_shortcodes');

//наш callback – uac_shortcode() – выводит результат работы шорткода.

function uac_shortcode($attr, $content = '')
{
    //$current_reader = wp_get_current_user();
	$current_role = uac_get_current_user_role();
	$user_access = ($current_role == 'super_client')  || ($current_role == 'administrator');
    if (!$user_access ) //($post_restricted == 1) &&
	{

        /* Return an alternate message. */
        return '<div align="center" style=" padding: 20px; border: 1px solid;">
					У Вас нет доступа к этой части страницы.
					<br/>
					<a style="font-size: 20px;" href="' .get_option('uac_key_insert_page'). '">Ввести ключ доступа</a>
				</div>';
		//unregister_sidebar( 'sidebar-1' );
    }
	else
	{
		return $content;
	}
}

/*function uac_page_title( $title ) {
	$title = '';

	return $title;
}*/




//вывод виджетов только на указанных страницах start
/*function wph_hide_widgets($instance, $widget, $args) {
    if ($widget->id_base == 'pages') {
        if (!is_page(array('6','1608'))) {return false;}
    }
}*/
//add_filter('widget_display_callback', 'wph_hide_widgets', 10, 3);
//вывод виджетов только на указанных страницах end
?>
