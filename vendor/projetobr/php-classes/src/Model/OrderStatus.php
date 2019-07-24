<?php

namespace Projeto\Model;

use Projeto\DB\Sql;
use Projeto\Model;

class OrderStatus extends Model{

	const EM_ABERTO = 1;
	const AGUARDANDO_PAGAMENTO = 2;
	const PAGO = 3;
	const ENTREGUE = 4;
}


?>