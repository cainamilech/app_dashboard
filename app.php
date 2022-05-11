<?php

//classe dashboard, criando o objeto
class Dashboard {

	public $data_inicio;
	public $data_fim;
	public $numeroVendas;
	public $totalVendas;

	public function __get($atributo){
		return $this->$atributo;
	}

	public function __set($atributo, $valor){
		$this->$atributo = $valor;
		return $this;
	}
}

//classe de conexao com o bd
class Conexao {
	private $host = 'localhost';
	private $dbname = 'dashboard';
	private $user = 'root';
	private $pass = '';

	public function conectar() {
		try {

			$conexao = new PDO(
				"mysql:host=$this->host;dbname=$this->dbname",
				"$this->user",
				"$this->pass"
			);

			//como estamos trabalhando com o mesmo tipo de caracter, podemos declarar
			$conexao->exec('set charset utf8');

			return $conexao;

		} catch (PDOException $e) {
			echo '<p>'.$e->getMessage().'</p>';
		}
	} 
}

//classe para manipular o objeto no banco (model)
class Bd {
	private $conexao;
	private $dashboard;

	public function __construct(Conexao $conexao,Dashboard $dashboard){
		$this->conexao = $conexao->conectar();
		$this->dashboard = $dashboard;
	}

	public function getNumeroVendas(){
		$query = '
			select
				count(*) as numero_vendas
			from
				tb_vendas
			where
				data_venda between :data_inicio and :data_fim;
		';

		//para nao ter problemas com injection
		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		//retornar um objeto e depois acessar o atributo numerovendas
		return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
	}

	public function getTotalVendas(){
		$query = '
			select
				SUM(total) as total_vendas
			from
				tb_vendas
			where
				data_venda between :data_inicio and :data_fim;
		';

		//para nao ter problemas com injection
		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		//retornar um objeto e depois acessar o atributo numerovendas
		return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
	}
}

//logica do script
$dashboard = new Dashboard();

$conexao = new Conexao();

//recuperar a competencia que foi recebida via get pelo script do front
//explode,tira o - e separa o mes e ano por indices
$competencia = explode('-', $_GET['competencia']);
//podemos entao atribuir o ano e o mes em variaveis, indicando o indice
$ano = $competencia[0];
$mes = $competencia[1];

//funcao nativa do php(cal_days_in_month(calendar, month, year), para saber quantos dia tem o mes, atribuindo a uma variavel
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

//formando as datas dinamicamente
$dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
$dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);

$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('totalVendas', $bd->getTotalVendas());
//transformar o texto em objeto literal
echo json_encode($dashboard);



?>