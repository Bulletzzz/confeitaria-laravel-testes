describe('Exercícios 2, 3 e 6: início e navegação', () => {
    beforeEach(() => cy.visit('/'));

    it('exibe Doce Gestão na página inicial', () => {
        cy.contains('[data-cy="brand"]', 'Doce Gestão').should('be.visible');
    });

    for (const [menu, rota] of [['products', 'produtos'], ['customers', 'clientes'], ['orders', 'pedidos'], ['deliveries', 'entregas']]) {
        it(`navega pelo menu para ${rota}`, () => {
            cy.porCy(`nav-${menu}`).click();
            cy.url().should('include', `/${rota}`);
        });
    }
});
