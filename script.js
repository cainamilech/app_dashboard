$(document).ready(() => {
	
	//selecionar o id do link
	//adicionar evento de click
	//tomar uma ação, no caso selecionando o id pagina
	//executar o metodo load, passando qual o conteudo carregar
	//no caso o documentacao.html
	//o resultado, é que o conteudo html na div #pagina é substituido por outro.
	$('#documentacao').on('click', () => {
		$('#pagina').load('documentacao.html')			
	})

	$('#suporte').on('click', () => {
		$('#pagina').load('suporte.html')
	})

	//alem do metodo load, esse carregamento do xml pode ser feito via post e get
	//$.post('documentacao.html', dados => {
			//$('#pagina').html(dados)
		//)}

	//$.get('suporte.html', dados => {
			//$('#pagina').html(dados)
		//)}


	//metodo ajax. para fazer o request, apartir da selecao de algum mes(competencia)
	//toda vez que é mudado, ele faz um novo request
	//selecionar o id, e na mudança(change) do select, dispara o evento, que é recuperar o value, atribuindo a uma variavel
	$('#competencia').on('change', e => {

		let competencia = $(e.target).val()
				
		//metodo, url, encaminhar ou nao dados, oq fazer se der sucesso, oq fazer se der erro
		$.ajax({
			type: 'GET',
			url: 'app.php',
			//transformar o texto em objeto literal, para poder pegar cada elemento,
			//podendo atribuir separadamente a cada pagina(categoria)
			dataType: 'json',
			data: `competencia=${competencia}`,
			success: dados => {
				//selecionar id do card no index,
				//atribuindo/substituindo como conteudo interno o valor da requisicao
				$('#numeroVendas').html(dados.numeroVendas)
				$('#totalVendas').html(dados.totalVendas)
			},
			erro: erro => {console.log(erro)},
		})

		//m
	})	
			
})

