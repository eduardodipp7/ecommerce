<?php if(!class_exists('Rain\Tpl')){exit;}?><!--<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
 Declaração do formulário -->  
<form method="post"  action="https://pagseguro.uol.com.br/v2/checkout/payment.html">  
          
        <!-- Campos obrigatórios -->  
        <INPUT TYPE="hidden" name="charset" value="utf-8">
        <input name="receiverEmail" type="hidden" value="eduardo.dipp7@gmail.com">  
        <input name="currency" type="hidden" value="BRL">  
  
        <!-- Itens do pagamento (ao menos um item é obrigatório) --> 
        <?php $counter1=-1;  if( isset($products) && ( is_array($products) || $products instanceof Traversable ) && sizeof($products) ) foreach( $products as $key1 => $value1 ){ $counter1++; ?> 
        <!-- Usando a variavel do rainTol {counter} para add mais um valor na varival name=itemId1 pra não duplicar com mesmo nome-->
        <!--Por padrão a $counter começa em 0 e por padrão do pagseguro tem que ser 1 por isso add +1-->
        <input name="itemId<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["idproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemDescription<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["desproduct"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemAmount<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["vltotal"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemQuantity<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["nrqtd"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="itemWeight<?php echo htmlspecialchars( $counter1+1, ENT_COMPAT, 'UTF-8', FALSE ); ?>" type="hidden" value="<?php echo htmlspecialchars( $value1["vlweight"]*1000, ENT_COMPAT, 'UTF-8', FALSE ); ?>"> 
       <?php } ?>
  
        <!-- Código de referência do pagamento no seu sistema (opcional) -->  
        <input name="reference" type="hidden" value="<?php echo htmlspecialchars( $order["idorder"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
          
        <!-- Informações de frete (opcionais) -->  
        <input name="shippingType" type="hidden" value="1">  
        <input name="shippingAddressPostalCode" type="hidden" value="<?php echo htmlspecialchars( $order["deszipcode"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressStreet" type="hidden" value="<?php echo htmlspecialchars( $order["desaddress"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressNumber" type="hidden" value="<?php echo htmlspecialchars( $order["desnumber"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressComplement" type="hidden" value="<?php echo htmlspecialchars( $order["descomplement"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressDistrict" type="hidden" value="<?php echo htmlspecialchars( $order["desdistrict"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressCity" type="hidden" value="<?php echo htmlspecialchars( $order["descity"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressState" type="hidden" value="<?php echo htmlspecialchars( $order["desstate"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="shippingAddressCountry" type="hidden" value="<?php echo htmlspecialchars( $order["descountry"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
  
        <!-- Dados do comprador (opcionais) -->  
        <input name="senderName" type="hidden" value="<?php echo htmlspecialchars( $order["desperson"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="senderAreaCode" type="hidden" value="<?php echo htmlspecialchars( $phone["areaCode"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="senderPhone" type="hidden" value="<?php echo htmlspecialchars( $phone["number"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
        <input name="senderEmail" type="hidden" value="<?php echo htmlspecialchars( $order["desemail"], ENT_COMPAT, 'UTF-8', FALSE ); ?>">  
  
        <!-- submit do form (obrigatório) 
        <input alt="Pague com PagSeguro" name="submit"  type="image"  
src="https://p.simg.uol.com.br/out/pagseguro/i/botoes/pagamentos/120x53-pagar.gif"/>  
--> 
          
</form>  

<!--Pega o primeiro formulario da pagina e da um submit automatico em ve de clicar com o botao-->
<script type="text/javascript">

        document.forms[0].submit();
        </script>