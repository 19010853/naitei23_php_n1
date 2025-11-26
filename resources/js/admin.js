import './bootstrap';

const AdminUI = {
    // Store timeout IDs for tooltip cleanup (moved to object scope to prevent memory leaks)
    tooltipTimeouts: new WeakMap(),
    
    // Constants
    TOOLTIP_DELAY: 1000, // 1 second delay before showing tooltip
    
    init() {
        this.bindSidebarToggle();
        this.bindDropdowns();
        this.registerModalHandlers();
        this.observeFilters();
        this.refreshStats();
        this.initTableTooltips();
        setInterval(() => this.refreshStats(), 60 * 1000);
    },

    bindSidebarToggle() {
        const shell = document.querySelector('.admin-shell');
        const toggleBtn = document.querySelector('[data-sidebar-toggle]');

        if (!shell || !toggleBtn) {
            return;
        }

        toggleBtn.addEventListener('click', () => {
            shell.classList.toggle('sidebar-collapsed');
        });
    },

    bindDropdowns() {
        document.addEventListener('click', (event) => {
            const dropdown = document.querySelector('.user-pill');
            const menu = document.querySelector('.user-menu');

            if (!dropdown || !menu) {
                return;
            }

            const clickedInside = dropdown.contains(event.target);

            if (clickedInside) {
                menu.classList.toggle('active');
            } else {
                menu.classList.remove('active');
            }
        });
    },

    registerModalHandlers() {
        document.querySelectorAll('[data-modal-trigger]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                const target = btn.getAttribute('data-modal-trigger');
                const modal = document.getElementById(target);
                if (modal) {
                    modal.classList.add('open');
                }
            });
        });

        document.querySelectorAll('.admin-modal').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal || event.target.hasAttribute('data-close-modal')) {
                    modal.classList.remove('open');
                }
            });
        });
    },

    observeFilters() {
        document.querySelectorAll('[data-table-search]').forEach((input) => {
            input.addEventListener('input', () => {
                const term = input.value.toLowerCase();
                const target = document.querySelector(input.dataset.tableSearch);
                if (!target) {
                    return;
                }

                target.querySelectorAll('tbody tr').forEach((row) => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        });
    },

    refreshStats() {
        if (!document.querySelector('[data-dashboard-stats]')) {
            return;
        }

        fetch('/admin/dashboard/stats', {
            headers: {
                Accept: 'application/json',
            },
        })
            .then((response) => response.json())
            .then((stats) => {
                document.querySelectorAll('[data-stat-key]').forEach((element) => {
                    const key = element.getAttribute('data-stat-key');
                    if (stats[key] !== undefined) {
                        element.textContent = new Intl.NumberFormat().format(stats[key]);
                    }
                });
            })
            .catch(() => {
                // fail quietly
            });
    },

    // Extract cell text content recursively
    extractCellText(element) {
        let text = '';
        const children = element.childNodes;
        children.forEach((child) => {
            if (child.nodeType === Node.TEXT_NODE) {
                const trimmed = child.textContent.trim();
                if (trimmed) text += trimmed + ' ';
            } else if (child.nodeType === Node.ELEMENT_NODE) {
                // Skip buttons, action elements, and tooltips
                const tagName = child.tagName.toLowerCase();
                if (!child.classList.contains('btn-action') && 
                    !child.classList.contains('cell-tooltip') &&
                    tagName !== 'button' &&
                    tagName !== 'script' &&
                    tagName !== 'style') {
                    const childText = this.extractCellText(child);
                    if (childText) text += childText + ' ';
                }
            }
        });
        return text.trim();
    },

    initTableTooltips() {
        // Function to add tooltips to all cells
        const addTooltipsToTable = (table) => {
            if (!table) return;

            const cells = table.querySelectorAll('tbody td');
            
            cells.forEach((cell) => {
                // Skip action buttons or empty states
                if (cell.querySelector('.btn-action') || 
                    cell.querySelector('button') ||
                    cell.classList.contains('empty-state')) {
                    return;
                }

                // Remove existing tooltip and clear any timeouts before re-initializing
                const existingTooltip = cell.querySelector('.cell-tooltip');
                if (existingTooltip) {
                    existingTooltip.remove();
                }
                
                // Clear any existing timeout
                const existingTimeout = AdminUI.tooltipTimeouts.get(cell);
                if (existingTimeout) {
                    clearTimeout(existingTimeout);
                    AdminUI.tooltipTimeouts.delete(cell);
                }

                // Check if cell or any child element has data-full-content attribute (for server-side truncated content)
                let fullText = cell.getAttribute('data-full-content');
                
                // Check nested elements for data-full-content
                if (!fullText || fullText.trim() === '') {
                    const nestedElement = cell.querySelector('[data-full-content]');
                    if (nestedElement) {
                        fullText = nestedElement.getAttribute('data-full-content');
                    }
                }
                
                // If no data attribute, get text from cell content
                if (!fullText || fullText.trim() === '') {
                    fullText = AdminUI.extractCellText(cell);
                }
                
                // Skip empty cells or cells with only whitespace
                if (!fullText || fullText.trim().length === 0) return;

                // Create tooltip element for ALL cells (not just truncated ones)
                const tooltip = document.createElement('div');
                tooltip.className = 'cell-tooltip';
                tooltip.textContent = fullText.trim();
                
                // Add tooltip to cell
                cell.style.position = 'relative';
                cell.appendChild(tooltip);

                // Add hover event listeners with delay
                cell.addEventListener('mouseenter', function() {
                    // Clear any existing timeout
                    const existingTimeout = AdminUI.tooltipTimeouts.get(cell);
                    if (existingTimeout) {
                        clearTimeout(existingTimeout);
                    }

                    // Set new timeout to show tooltip after delay
                    const timeoutId = setTimeout(() => {
                        tooltip.classList.add('show');
                    }, AdminUI.TOOLTIP_DELAY);

                    AdminUI.tooltipTimeouts.set(cell, timeoutId);
                });

                cell.addEventListener('mouseleave', function() {
                    // Clear timeout if user leaves before delay
                    const timeoutId = AdminUI.tooltipTimeouts.get(cell);
                    if (timeoutId) {
                        clearTimeout(timeoutId);
                        AdminUI.tooltipTimeouts.delete(cell);
                    }

                    // Hide tooltip immediately
                    tooltip.classList.remove('show');
                });
            });
        };

        // Initialize tooltips for all tables
        const tables = document.querySelectorAll('.admin-table');
        tables.forEach((table) => {
            addTooltipsToTable(table);
        });

        // Re-initialize tooltips when table content changes (for AJAX-loaded content)
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            const newTables = node.querySelectorAll ? node.querySelectorAll('.admin-table') : [];
                            newTables.forEach((table) => {
                                addTooltipsToTable(table);
                            });
                            if (node.classList && node.classList.contains('admin-table')) {
                                addTooltipsToTable(node);
                            }
                        }
                    });
                }
            });
        });

        // Observe changes to table wrappers
        document.querySelectorAll('.table-wrapper').forEach((wrapper) => {
            observer.observe(wrapper, {
                childList: true,
                subtree: true
            });
        });
    },
};

document.addEventListener('DOMContentLoaded', () => {
    AdminUI.init();
});

// Export AdminUI to window for global access
window.AdminUI = AdminUI;

