const searchInput = document.querySelector('#product-search');
const productCards = [...document.querySelectorAll('.product-card')];
const categoryButtons = [...document.querySelectorAll('.category-chip')];
const noResults = document.querySelector('#no-results');
let activeCategory = 'all';

function filterProducts() {
  if (!productCards.length) return;

  const searchTerm = (searchInput?.value || '').trim().toLocaleLowerCase('vi');
  let visibleCount = 0;

  for (const card of productCards) {
    const matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
    const matchesSearch = (card.dataset.search || '').toLocaleLowerCase('vi').includes(searchTerm);
    const visible = matchesCategory && matchesSearch;
    card.hidden = !visible;
    if (visible) visibleCount += 1;
  }

  if (noResults) noResults.hidden = visibleCount > 0;
}

for (const button of categoryButtons) {
  button.addEventListener('click', () => {
    activeCategory = button.dataset.category || 'all';
    for (const item of categoryButtons) item.classList.toggle('is-active', item === button);
    filterProducts();
  });
}

searchInput?.addEventListener('input', filterProducts);