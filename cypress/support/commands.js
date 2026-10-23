Cypress.Commands.add('porCy', (selector) => cy.get(`[data-cy="${selector}"]`));

Cypress.Commands.add('preencherProduto', (dados) => {
    for (const field of ['name', 'description', 'price', 'stock', 'minimum_stock']) {
        cy.porCy(`product-${field}`).clear().type(String(dados[field]));
    }
    cy.porCy('product-category').select(dados.category);
    cy.porCy('product-active')[dados.active === false ? 'uncheck' : 'check']();
});

Cypress.Commands.add('criarProduto', (dados) => {
    cy.visit('/produtos/create');
    cy.preencherProduto(dados);
    cy.porCy('product-save').click();
    cy.location('pathname').should('eq', '/produtos');
    cy.porCy('success-message').should('contain', 'Produto cadastrado com sucesso.');
    return cy.contains('[data-cy="product-row"]', dados.name).should('be.visible');
});

Cypress.Commands.add('criarCliente', (dados) => {
    cy.visit('/clientes/create');
    for (const field of ['name', 'email', 'phone', 'address', 'city']) {
        cy.porCy(`customer-${field}`).clear().type(dados[field]);
    }
    cy.porCy('customer-save').click();
    cy.location('pathname').should('eq', '/clientes');
    cy.porCy('success-message').should('contain', 'Cliente cadastrado com sucesso.');
    return cy.contains('[data-cy="customer-row"]', dados.name).should('be.visible');
});

Cypress.Commands.add('criarPedido', ({ cliente, produto, quantidade = 3, status = 'confirmed' }) => {
    cy.visit('/pedidos/create');
    cy.porCy('order-customer_id').select(cliente);
    cy.porCy('order-product_id').find('option').contains(produto).then(($option) => {
        cy.porCy('order-product_id').select($option.val());
    });
    cy.porCy('order-quantity').clear().type(String(quantidade));
    cy.porCy('order-status').select(status);
    cy.porCy('order-save').click();
});

Cypress.Commands.add('criarFrete', (cliente) => {
    cy.visit('/entregas/create');
    cy.porCy('delivery-order_id').find('option').contains(cliente).then(($option) => {
        cy.porCy('delivery-order_id').select($option.val());
    });
    cy.porCy('delivery-address').type('Rua dos Doces, 123');
    cy.porCy('delivery-city').type('São Paulo');
    cy.porCy('delivery-fee').clear().type('12.50');
    cy.porCy('delivery-scheduled_at').type('2026-12-01');
    cy.porCy('delivery-tracking_code').type('DOCE-CYPRESS-123');
    cy.porCy('delivery-status').select('scheduled');
    cy.porCy('delivery-save').click();
    cy.location('pathname').should('eq', '/entregas');
    cy.contains('[data-cy="delivery-row"]', cliente).should('contain', 'scheduled');
});
