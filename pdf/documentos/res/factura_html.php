<style type="text/css">
	<!--
	table { vertical-align: top; }
	tr    { vertical-align: top; }
	td    { vertical-align: top; }
	.midnight-blue{
		background:#2c3e50;
		padding: 4px 4px 4px;
		color:white;
		font-weight:bold;
		font-size:12px;
	}
	.silver{
		background:white;
		padding: 3px 4px 3px;
	}
	.clouds{
		background:#ecf0f1;
		padding: 3px 4px 3px;
	}
	.border-top{
		border-top: solid 1px #bdc3c7;

	}
	.border-left{
		border-left: solid 1px #bdc3c7;
	}
	.border-right{
		border-right: solid 1px #bdc3c7;
	}
	.border-bottom{
		border-bottom: solid 1px #bdc3c7;
	}
	table.page_footer {width: 100%; border: none; background-color: white; padding: 2mm;border-collapse:collapse; border: none;}
}
-->
</style>
<page backtop="15mm" backbottom="15mm" backleft="15mm" backright="15mm" style="font-size: 12pt; font-family: arial" >
	<page_footer>
	<table class="page_footer">
		<tr>

			<td style="width: 15%; text-align: left;font-size:11px">
				P&aacute;gina [[page_cu]]/[[page_nb]]
			</td>
			<td style="width: 85%; text-align: right;color:#696969;font-size:10px">
				&copy; <?php echo  $anio=date('Y'); echo " - Autorizado mediante resolución #11-97 de la Dirección General de Tributación Directa, publicado en la Gaceta #171 del 5 septiembre de 1997. "; ?>
			</td>
		</tr>
	</table>
	</page_footer>
	<?php include("encabezado_factura.php");?>
	<br>


	
	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
		<tr>
			<td style="width:50%;" class='midnight-blue'>FACTURAR A</td>
		</tr>
		<tr>
			<td style="width:50%;" >
				<?php 
				//Moneda de la factura
				$moneda=intval($_GET['moneda']);
				$sql_cliente=mysqli_query($con,"select * from clientes where id_cliente='$id_cliente'");
				$rw_cliente=mysqli_fetch_array($sql_cliente);
				echo $rw_cliente['nombre_cliente'];
				echo "<br>Cédula: ";
				echo $rw_cliente['cedula'];
				echo "<br>";
				echo $rw_cliente['direccion_cliente'];
				echo "<br> Teléfono: ";
				echo $rw_cliente['telefono_cliente'];
				echo "<br> Email: ";
				echo $rw_cliente['email_cliente'];
				?>

			</td>
		</tr>


	</table>

	<br>
	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
		<tr>
			<td style="width:25%;" class='midnight-blue'>Vendedor</td>
			<td style="width:25%;" class='midnight-blue'>Fecha</td>
			<td style="width:30%;" class='midnight-blue'>Forma de pago</td>
			<td style="width:20%;" class='midnight-blue'>Moneda</td>
		</tr>
		<tr>
			<td style="width:25%;">
				<?php 
				$sql_user=mysqli_query($con,"select * from users where user_id='$id_vendedor'");
				$rw_user=mysqli_fetch_array($sql_user);
				echo $rw_user['firstname']." ".$rw_user['lastname'];
				?>
			</td>
			<td style="width:25%;"><?php echo date("d/m/Y");?></td>
			<td style="width:30%;" >
				<?php 
				if ($condiciones==1){echo "Efectivo";}
				elseif ($condiciones==2){echo "Cheque";}
				elseif ($condiciones==3){echo "Transferencia bancaria";}
				elseif ($condiciones==4){echo "Crédito 30 días";}
				?>
			</td>
			<td style="width:20%;" ><?php if($moneda == 1){ echo "Dólares"; } else { echo "Colones"; } ?></td>
		</tr>
		


	</table>
	<br>

	<table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;">
		<tr>
			<th style="width: 10%;text-align:center" class='midnight-blue'>Cantidad</th>
			<th style="width: 60%" class='midnight-blue'>Descripción</th>
			<th style="width: 15%;text-align: right" class='midnight-blue'>Precio Unit.</th>
			<th style="width: 15%;text-align: right" class='midnight-blue'>Precio Total</th>

		</tr>

		<?php
		$cambio = getVentaDolarColones();
		$nums=1;
		$sumador_total=0;
		$sql=mysqli_query($con, "select * from products, tmp where products.id_producto=tmp.id_producto and tmp.session_id='".$session_id."' and tmp.moneda_tmp=".$moneda);
		while ($row=mysqli_fetch_array($sql))
		{
			$id_tmp=$row["id_tmp"];
			$id_producto=$row["id_producto"];
			$codigo_producto=$row['codigo_producto'];
			$cantidad=$row['cantidad_tmp'];
			$nombre_producto=$row['nombre_producto'];

			$precio_venta=$row['precio_tmp'];
	$precio_venta_f=number_format($precio_venta,2);//Formateo variables
	$precio_venta_r=str_replace(",","",$precio_venta_f);//Reemplazo las comas
	$precio_total=$precio_venta_r*$cantidad;
	$precio_total_f=number_format($precio_total,2);//Precio total formateado
	$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
	$sumador_total+=$precio_total_r;//Sumador
	if ($nums%2==0){
		$clase="clouds";
	} else {
		$clase="silver";
	}
	?>

	<tr>
		<td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
		<td class='<?php echo $clase;?>' style="width: 60%; text-align: left"><?php echo $nombre_producto;?></td>
		<td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_venta_f;?></td>
		<td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_total_f;?></td>

	</tr>

	<?php 
	//Insert en la tabla detalle_cotizacion
	$insert_detail=mysqli_query($con, "INSERT INTO detalle_factura VALUES ('','$numero_factura','$id_producto','$cantidad','$precio_venta_r')");
	
	$nums++;
}
if($impuestoValue == 2){
	$impuesto=0.00;
}else{
	$impuesto=get_row('perfil','impuesto', 'id_perfil', 1);
}

$subtotal=number_format($sumador_total,2,'.','');
$total_iva=($subtotal * $impuesto )/100;
$total_iva=number_format($total_iva,2,'.','');
$total_factura=$subtotal+$total_iva;

if($moneda == 1){
	$total_colones=$total_factura*$cambio;
} else { 
	$total_colones=$total_factura;
}

$total_colones_f=number_format($total_colones,2);
$total_colones_r=str_replace(",","",$total_colones_f);

?>

<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Subtotal:</td>
	<td style="widtd: 15%; text-align: right;"> <?php echo $simbolo_moneda;?><?php echo number_format($subtotal,2);?></td>
</tr>
<?php if($impuesto > 0){?>
<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Imp. Ventas: </td>
	<td style="widtd: 15%; text-align: right;"> <?php echo $impuesto;?>%</td>
</tr>
<? } ?>
<tr>
	<td colspan="3" style="widtd: 85%; text-align: right;">Total: </td>
	<td style="widtd: 15%; text-align: right;"><?php echo $simbolo_moneda;?><?php echo number_format($total_factura,2);?></td>
</tr>
</table>
<?php if($moneda == 1){ ?>
<table cellspacing="0" style="width: 100%; text-align: left; font-size: 8pt;margin-top: 0px;border:none;">
	<tr>
		<td style="width: 50%; text-align: left;border-top:1px;border-top-color:#D3D3D3">Tipo de cambio: ¢<?php echo number_format($cambio,2);?>&nbsp; Total en colones: <?php echo $total_colones_f;?></td>
		<td style="width: 25%; text-align: center;border-top:1px;border-top-color:#D3D3D3"></td>
		<td style="width: 25%; text-align: center;border-top:1px;border-top-color:#D3D3D3"></td>
	</tr>
</table>
<?php } ?>
<table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;margin-top: 60px;border:none;">
	<tr>
		<td style="width: 100%; text-align: left;">¡Gracias por su preferencia!</td>
	</tr>
</table>
<table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;margin-top: 20px;border:1px;border-color: #778899">
	<tr>
		<td style="width: 100%; text-align: left;"><?php echo get_row('perfil','mensaje_factura', 'id_perfil', 1);?></td>
	</tr>
</table>


<br>

</page>

<?php
$date=date("d-m-Y");
if($moneda == 1){
	$insert=mysqli_query($con,"INSERT INTO facturas VALUES (NULL,'$numero_factura','$date','$id_cliente','$id_vendedor','$condiciones','$total_factura','1','$total_colones_r','$cambio','$impuesto', '$moneda')");	
}else{
	$insert=mysqli_query($con,"INSERT INTO facturas VALUES (NULL,'$numero_factura','$date','$id_cliente','$id_vendedor','$condiciones','$total_factura','1','$total_colones_r','$cambio','$impuesto', '$moneda')");	
}

$delete=mysqli_query($con,"DELETE FROM tmp WHERE session_id='".$session_id."'");
?>