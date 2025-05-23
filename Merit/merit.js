document.addEventListener('DOMContentLoaded', () => {
  const table = document.querySelector('table');
  const headers = table.querySelectorAll('thead th');
  const tbody = table.querySelector('tbody');

  headers.forEach((header, index) => {
    header.addEventListener('click', () => {
      const rows = Array.from(tbody.querySelectorAll('tr'));
      const isAscending = header.classList.contains('asc');

      rows.sort((rowA, rowB) => {
        const cellA = rowA.children[index].innerText.toLowerCase();
        const cellB = rowB.children[index].innerText.toLowerCase();

        // Numeric sort if both values are numbers
        if (!isNaN(cellA) && !isNaN(cellB)) {
          return isAscending ? cellA - cellB : cellB - cellA;
        }

        // String sorting
        if (cellA < cellB) return isAscending ? 1 : -1;
        if (cellA > cellB) return isAscending ? -1 : 1;
        return 0;
      });

      // Remove sorting classes from all headers
      headers.forEach(th => th.classList.remove('asc', 'desc'));

      // Toggle sorting direction classes
      header.classList.toggle('asc', !isAscending);
      header.classList.toggle('desc', isAscending);

      // Rebuild tbody
      rows.forEach(row => tbody.appendChild(row));
    });
  });
});

