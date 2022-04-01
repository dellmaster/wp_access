<?php
//Создание таблицы в БД с данными тарифов
function uac_tarifs_db_create($db_prefix)
{
	$table_name = $db_prefix.'uac_tarifs';
	//if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) //Проверяем есть ли уже такая таблица
	 //{//Если нет - создаем таблицу для ключей
		$sql = "CREATE TABLE  $table_name ( tarif_id int(11) NOT NULL AUTO_INCREMENT, tarif_name varchar(32) DEFAULT NULL, tarif_srok_hours int(11) DEFAULT NULL , tarif_srok_month int(11) DEFAULT NULL , tarif_active tinyint(1) NOT NULL DEFAULT '1', tarif_sidebars_closed text DEFAULT NULL, tarif_widgets_closed text DEFAULT NULL,  UNIQUE KEY id (tarif_id) )
		ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

	 //}
	
}


function get_uac_tarifs_control()
{
	global $wpdb;
	$table_name = $wpdb->prefix.'uac_tarifs';
	//mysql_query('SET names "utf8"');

	$new_tarif_save_errors = '';
	$new_tarif_save_massege = "";
	if (isset($_POST['uac_new_tarif_save'])) // Сохранение нового тарифа
	{
		if (isset($_POST['uac_new_tarif_name']) && ($_POST['uac_new_tarif_name'] != ''))
		{
			$uac_new_tarif_name = $_POST['uac_new_tarif_name'];
			//$uac_new_tarif_name = $wpdb->escape($uac_new_tarif_name);
		}
		else
		{
			$uac_new_tarif_name = '';
			$new_tarif_save_errors = 'Введите название тарифа.</br>';
		}

		if (isset($_POST['uac_new_tarif_srok_hours']) && ($_POST['uac_new_tarif_srok_hours'] != ''))
		{
			$uac_new_tarif_srok_hours = $wpdb->escape($_POST['uac_new_tarif_srok_hours']);
		}
		else
		{
			$uac_new_tarif_srok_hours = 0;
			$new_tarif_save_errors = 'Введите срок действия ключей.</br>';
		}

		if (isset($_POST['uac_new_tarif_active']) && ($_POST['uac_new_tarif_active'] != ''))
		{
			$uac_new_tarif_active = 1;
		}
		else
		{
			$uac_new_tarif_active = 0;
		}

		if($new_tarif_save_errors == '')
		{
			$wpdb->query('SET NAMES utf8');
			$uac_query = "INSERT INTO $table_name (`tarif_name`, `tarif_srok_hours`, `tarif_active`) VALUES ('$uac_new_tarif_name', $uac_new_tarif_srok_hours, $uac_new_tarif_active)";
			echo $uac_query;
			$wpdb->query($uac_query);
			$new_tarif_save_massege = "Тариф сохранен.";
		}

	}

	if (isset($_POST['uac_tarif_edit']))
	{
		foreach ($_POST['uac_tarif_edit'] as $key => $value)
			{
				echo ' -'.$key.'- ';
			}
	}
	$keys_query = "SELECT * FROM $table_name ORDER BY tarif_id ASC";
	$result = $wpdb->get_results($keys_query, ARRAY_A);

	?>
	<div style="text-align: center;">
	<h3>Тарифы</h3>
	<form method="post" action="">
	<table border="1" style="border-style: solid" align="center">

		<thead><tr><th>Изменить</th><th>ID</th><th>Тариф</th><th>Срок, часов</th><th>Действует?</th><th>Ключей всего</th><th>Ключей свободно</th><th>Удалить</th</tr></thead>
		<tbody>
		<?
			foreach ($result as $uac_row)
			{
				$uac_key_used_text =  $uac_row['key_used'] ? 'Да' : 'Нет';
				echo '<tr><td><input type="submit" name="uac_tarif_edit['.$uac_row['tarif_id'].']" value="Изменить"></td><td>'.$uac_row['tarif_id'].'</td><td>'.$uac_row['tarif_name'].'</td><td>'.$uac_row['tarif_srok_hours'] .'</td><td>'.$uac_row['tarif_active'].'</td> <td>'.'</td> <td>'.'</td></tr>';
			}
			?>
		</tbody>
	</table>
	</form>
	</br>
	</br>
	<h3>Добавить тариф</h3>
	<form method="post" action="">
		Название тарифа: <input type="text" size="70" name="uac_new_tarif_name" value="" required />
		</br>
		</br>
		Срок действия ключей в часах: <input type="text" size="70" name="uac_new_tarif_srok_hours" value="0" placeholder="Введите срок в течении которого будут действовать ключи. 0 - срок не ограничен." required pattern="[0-9]+$" />
		</br>
		1 сутки = 24 часа. 30 дней = 720 часов.
		</br>
		Если срок равено "0" - срок ключей не ограничен.

		</br>
		</br>
		Действует: <input type='checkbox' name='uac_new_tarif_active' value='1'  checked/>
		</br>
		<?echo $new_tarif_save_massege;?>
		</br>
		<input type="submit" name="uac_new_tarif_save" value="Добавить">
	</form>
	</div>




	<?


}

?>
