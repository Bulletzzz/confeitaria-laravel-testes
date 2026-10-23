describe('Exercícios 4 a 11, 13 e 14: produtos e validação', () => {
    beforeEach(() => {
        cy.fixture('produto').as('produto');
        cy.visit('/produtos');
    });

    it('exibe tabela, dados do seed e cabeçalhos', () => {
        cy.porCy('product-table').should('be.visible');
        cy.porCy('product-row').should('have.length.at.least', 1);
        for (const titulo of ['Nome', 'Categoria', 'Preço', 'Estoque']) {
            cy.porCy('product-table').find('th').should('contain', titulo);
        }
        cy.contains('Bolo de chocolate').should('be.visible');
    });

    it('abre o formulário com os cinco campos obrigatórios', () => {
        cy.porCy('product-create').click();
        cy.url().should('include', '/produtos/create');
        for (const field of ['name', 'category', 'price', 'stock', 'minimum_stock']) {
            cy.porCy(`product-${field}`).should('be.visible').and('have.attr', 'required');
        }
        cy.porCy('product-category').should('have.prop', 'tagName', 'SELECT');
    });

    it('cadastra produto usando fixture e comando customizado', function () {
        cy.criarProduto(this.produto);
        cy.contains('[data-cy="product-row"]', this.produto.name).should('contain', '45,90');
    });

    it('mostra o erro de cada campo obrigatório ao enviar vazio', () => {
        cy.visit('/produtos/create');
        for (const field of ['stock', 'minimum_stock']) cy.porCy(`product-${field}`).clear();
        // Permite enviar ao servidor para verificar a validação Laravel além da validação HTML.
        cy.porCy('product-form').invoke('attr', 'novalidate', 'novalidate');
        cy.porCy('product-save').click();
        for (const field of ['name', 'category', 'price', 'stock', 'minimum_stock']) {
            cy.porCy(`error-${field}`).should('be.visible').and('contain', 'required');
        }
    });

    for (const [field, valor, cenario] of [
        ['price', '-1', 'preço negativo'],
        ['stock', '-1', 'estoque negativo'],
        ['price', 'abc', 'texto no preço'],
        ['stock', 'abc', 'texto no estoque'],
    ]) {
        it(`rejeita ${cenario}`, function () {
            cy.visit('/produtos/create');
            cy.preencherProduto({ ...this.produto, name: `Inválido ${cenario}` });
            cy.porCy('product-form').invoke('attr', 'novalidate', 'novalidate');
            if (valor === 'abc') cy.porCy(`product-${field}`).invoke('attr', 'type', 'text');
            cy.porCy(`product-${field}`).clear().type(valor);
            cy.porCy('product-save').click();
            cy.porCy(`error-${field}`).should('be.visible');
            cy.location('pathname').should('eq', '/produtos/create');
            cy.visit('/produtos');
            cy.porCy('product-table').should('not.contain', `Inválido ${cenario}`);
        });
    }

    it('edita o preço de um produto existente', function () {
        const produto = { ...this.produto, name: 'Produto para editar' };
        cy.criarProduto(produto);
        cy.contains('[data-cy="product-row"]', produto.name).find('[data-cy="product-edit"]').click();
        cy.porCy('product-price').clear().type('59.90');
        cy.porCy('product-save').click();
        cy.porCy('success-message').should('contain', 'Produto atualizado');
        cy.contains('[data-cy="product-row"]', produto.name).should('contain', '59,90');
    });

    it('exclui um produto criado pelo teste e confirma a remoção', function () {
        const produto = { ...this.produto, name: 'Produto para excluir' };
        cy.criarProduto(produto);
        cy.on('window:confirm', () => true);
        cy.contains('[data-cy="product-row"]', produto.name).find('[data-cy="product-delete"]').click();
        cy.porCy('success-message').should('contain', 'Produto removido');
        cy.porCy('product-table').should('not.contain', produto.name);
    });

    it('exibe alerta apenas quando o estoque está baixo', function () {
        cy.criarProduto({ ...this.produto, name: 'Estoque baixo Cypress', stock: 2, minimum_stock: 5 });
        cy.contains('[data-cy="product-row"]', 'Estoque baixo Cypress').within(() => {
            cy.porCy('stock-badge').should('have.attr', 'data-low-stock', 'true');
            cy.porCy('low-stock-alert').should('be.visible');
        });
        cy.criarProduto({ ...this.produto, name: 'Estoque normal Cypress', stock: 20, minimum_stock: 5 });
        cy.contains('[data-cy="product-row"]', 'Estoque normal Cypress').within(() => {
            cy.porCy('stock-badge').should('have.attr', 'data-low-stock', 'false');
            cy.porCy('low-stock-alert').should('not.exist');
        });
    });
});
