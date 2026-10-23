describe('Exercício 16: paginação', () => {
    beforeEach(() => cy.visit('/produtos'));

    it('cria mais de dez produtos, avança e retorna à primeira página', () => {
        cy.fixture('produto').then((produto) => {
            for (let i = 1; i <= 11; i++) {
                cy.criarProduto({ ...produto, name: `Produto paginado ${String(i).padStart(2, '0')}` });
            }
        });
        cy.visit('/produtos');
        cy.porCy('product-row').should('have.length', 10);
        cy.porCy('product-row').first().invoke('attr', 'data-id').then((primeiroId) => {
            cy.porCy('pagination-next').click();
            cy.url().should('include', 'page=2');
            cy.porCy('product-row').first().should('not.have.attr', 'data-id', primeiroId);
            cy.porCy('pagination-previous').click();
            cy.url().should('include', 'page=1');
            cy.porCy('product-row').first().should('have.attr', 'data-id', primeiroId);
        });
    });
});
