// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A javascript module to handle some UI on the STACK dashboard.
 *
 * @module     qtype_stack/dashboard
 * @copyright  2026 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function () {

    /**
     * Sets up.
     *
     */
    function init() {
        // Un-disable tabs once the page has loaded.
        const tabs = document.querySelectorAll('.stack-dash-tab-load');
        for (const tab of tabs) {
            tab.classList.remove("disabled", "stack-dash-tab-load");
        }

        // Add simple client-side sorting for the first two columns.
        const variantsTable = document.getElementById('deployed-variants-table');
        if (!variantsTable) {
            return;
        }
        const sortableHeaders = variantsTable.querySelectorAll('.stack-sortable-header');
        const toggleVariantsButton = variantsTable.querySelector('.stack-toggle-variants-btn');

        /**
         * Update arrow icons showing direction of sort
         *
         * @param {element} activeHeader Header element to update
         * @param {boolean} isAsc is column sorted in ascending order?
         */
        function setHeaderState(activeHeader, isAsc) {
            sortableHeaders.forEach((header) => {
                const arrowIcon = header.querySelector('.stack-sort-arrow i');
                if (header === activeHeader) {
                    header.setAttribute('aria-sort', isAsc ? 'ascending' : 'descending');
                    header.dataset.sortDirection = isAsc ? 'asc' : 'desc';
                    if (arrowIcon) {
                        arrowIcon.classList.remove('fa-sort', 'fa-sort-up', 'fa-sort-down', 'text-muted');
                        arrowIcon.classList.add(isAsc ? 'fa-sort-up' : 'fa-sort-down');
                    }
                } else {
                    header.setAttribute('aria-sort', 'none');
                    delete header.dataset.sortDirection;
                    if (arrowIcon) {
                        arrowIcon.classList.remove('fa-sort-up', 'fa-sort-down');
                        arrowIcon.classList.add('fa-sort', 'text-muted');
                    }
                }
            });
        }

        /**
         * Get test content of index cell or row.
         *
         * @param {int} row
         * @param {int} index
         */
        function getCellValue(row, index) {
            const cell = row.cells[index];
            return cell ? cell.textContent.trim() : '';
        }

        /**
         * Sort table by a column.
         *
         * @param {element} header The header element mof the column.
         */
        function sortByHeader(header) {
            const columnIndex = Number(header.dataset.sortIndex);
            const sortType = header.dataset.sortType;
            const currentDirection = header.dataset.sortDirection || 'none';
            // Flip current direction.
            const isAsc = currentDirection !== 'asc';
            const direction = isAsc ? 1 : -1;
            // Filters out header row.
            const rows = Array.from(variantsTable.querySelectorAll('tr')).filter((row) => row.querySelector('td'));

            rows.sort((a, b) => {
                const av = getCellValue(a, columnIndex);
                const bv = getCellValue(b, columnIndex);

                if (sortType === 'number') {
                    const an = Number(av);
                    const bn = Number(bv);
                    const aIsNum = !Number.isNaN(an);
                    const bIsNum = !Number.isNaN(bn);
                    if (aIsNum && bIsNum) {
                        return (an - bn) * direction;
                    }
                    return av.localeCompare(bv, undefined, { numeric: true, sensitivity: 'base' }) * direction;
                }

                return av.localeCompare(bv, undefined, { sensitivity: 'base' }) * direction;
            });

            rows.forEach((row) => variantsTable.appendChild(row));
            setHeaderState(header, isAsc);
        }

        // Add event listeners to headers.
        sortableHeaders.forEach((header) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => sortByHeader(header));
            header.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    sortByHeader(header);
                }
            });
        });

        // Assume question note starts in ascending order and show that state initially.
        const defaultSortedHeader = variantsTable.querySelector('.stack-sortable-header[data-sort-index="1"]');
        if (defaultSortedHeader) {
            setHeaderState(defaultSortedHeader, true);
        }

        // Prevent toggle button sorting column. Add event listener to toggle selection boxes.
        if (toggleVariantsButton) {
            toggleVariantsButton.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const checkboxes = Array.from(variantsTable.querySelectorAll('input[type="checkbox"][name^="selectvariant-"]'));
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = !checkbox.checked;
                });
            });
        }
    }

    /** Export our entry point. */
    return {
        init: init
    };
});
