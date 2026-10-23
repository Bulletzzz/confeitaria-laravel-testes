describe('Exercícios 17 a 20: pedidos, estoque e fretes ponta a ponta', () => {
    beforeEach(() => {
        cy.fixture('produto').as('produto');
        cy.fixture('cliente').as('cliente');
        cy.visit('/');
    });

    it('cria pedido, valida status e confere a baixa de estoque', function () {
        const produto = { ...this.produto, name: 'Produto do pedido', stock: 10 };
        const cliente = { ...this.cliente, name: 'Cliente do pedido' };
        cy.criarCliente(cliente);
        cy.criarProduto(produto);
        cy.contains('[data-cy="product-row"]', produto.name).find('[data-cy="stock-badge"]')
            .invoke('attr', 'data-stock').then(Number).as('estoqueAntes');
        cy.criarPedido({ cliente: cliente.name, produto: produto.name, quantidade: 3 });
        cy.porCy('success-message').should('contain', 'Pedido criado e estoque atualizado');
        cy.contains('[data-cy="order-row"]', cliente.name)
            .should('contain', `${produto.name} (3)`).find('[data-cy="order-status"]').should('have.text', 'confirmed');
        cy.visit('/produtos');
        cy.get('@estoqueAntes').then((estoqueAntes) => {
            cy.contains('[data-cy="product-row"]', produto.name).find('[data-cy="stock-badge"]')
                .should('have.attr', 'data-stock', String(estoqueAntes - 3));
        });
    });

    it('rejeita pedido com estoque insuficiente e mantém a quantidade', function () {
        const produto = { ...this.produto, name: 'Produto sem estoque suficiente', stock: 2 };
        const cliente = { ...this.cliente, name: 'Cliente sem estoque' };
        cy.criarCliente(cliente);
        cy.criarProduto(produto);
        cy.criarPedido({ cliente: cliente.name, produto: produto.name, quantidade: 3 });
        cy.porCy('error-quantity').should('contain', 'Estoque insuficiente');
        cy.visit('/pedidos');
        cy.porCy('order-table').should('not.contain', cliente.name);
        cy.visit('/produtos');
        cy.contains('[data-cy="product-row"]', produto.name).find('[data-cy="stock-badge"]')
            .should('have.attr', 'data-stock', '2');
    });

    it('executa cliente → produto → pedido → frete e acompanha até a entrega', function () {
        const produto = { ...this.produto, name: 'Produto fluxo E2E' };
        const cliente = { ...this.cliente, name: 'Cliente fluxo E2E' };
        cy.criarCliente(cliente);
        cy.criarProduto(produto);
        cy.intercept('POST', '**/pedidos').as('criarPedido');
        cy.criarPedido({ cliente: cliente.name, produto: produto.name, quantidade: 2 });
        cy.wait('@criarPedido').then(({ request, response }) => {
            expect(response.statusCode).to.eq(302);
            expect(decodeURIComponent(request.body)).to.include('quantity=2');
        });
        cy.porCy('success-message').should('contain', 'Pedido criado');
        cy.contains('[data-cy="order-row"]', cliente.name).should('contain', 'confirmed');
        cy.criarFrete(cliente.name);
        cy.porCy('success-message').should('contain', 'Entrega cadastrada');
        cy.contains('[data-cy="delivery-row"]', cliente.name)
            .should('contain', 'Rua dos Doces, 123').and('contain', '12,50').and('contain', '01/12/2026');
        for (const status of ['in_transit', 'delivered']) {
            cy.contains('[data-cy="delivery-row"]', cliente.name).find('[data-cy="delivery-edit"]').click();
            cy.porCy('delivery-tracking_code').should('have.value', 'DOCE-CYPRESS-123');
            cy.porCy('delivery-status').select(status);
            cy.porCy('delivery-save').click();
            cy.porCy('success-message').should('contain', 'Entrega atualizada');
            cy.contains('[data-cy="delivery-row"]', cliente.name).find('[data-cy="delivery-status"]')
                .should('have.text', status);
        }
    });
});
