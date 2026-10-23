describe('Exercícios 12, 14 e 15: CRUD de clientes', () => {
    beforeEach(() => {
        cy.fixture('cliente').as('cliente');
        cy.visit('/clientes');
    });

    it('cria, edita e exclui cliente com contato e endereço', function () {
        cy.criarCliente(this.cliente);
        cy.contains('[data-cy="customer-row"]', this.cliente.name)
            .should('contain', this.cliente.email).and('contain', this.cliente.phone)
            .and('contain', this.cliente.address).and('contain', this.cliente.city)
            .find('[data-cy="customer-edit"]').click();
        cy.porCy('customer-phone').clear().type('(11) 98888-4321');
        cy.porCy('customer-address').clear().type('Rua Atualizada, 200');
        cy.porCy('customer-save').click();
        cy.porCy('success-message').should('contain', 'Cliente atualizado');
        cy.contains('[data-cy="customer-row"]', this.cliente.name)
            .should('contain', '(11) 98888-4321').and('contain', 'Rua Atualizada, 200');
        cy.on('window:confirm', () => true);
        cy.contains('[data-cy="customer-row"]', this.cliente.name).find('[data-cy="customer-delete"]').click();
        cy.porCy('success-message').should('contain', 'Cliente removido');
        cy.porCy('customer-table').should('not.contain', this.cliente.name);
    });
});
