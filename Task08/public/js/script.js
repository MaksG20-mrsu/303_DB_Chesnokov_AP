/**
 * Основной файл JavaScript для приложения управления студентами и экзаменами
 */

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация всех компонентов
    initFilters();
    initForms();
    initConfirmations();
    initDatePickers();
    initDynamicSelects();
    initTableSorting();
    initNotifications();
});

/**
 * Инициализация фильтров
 */
function initFilters() {
    const filterForms = document.querySelectorAll('.filter-form');
    
    filterForms.forEach(form => {
        form.addEventListener('change', function() {
            // Автоматическая отправка формы при изменении фильтра
            if (this.hasAttribute('data-auto-submit')) {
                this.submit();
            }
        });
        
        // Кнопка сброса фильтра
        const resetBtn = form.querySelector('.btn-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                form.reset();
                form.submit();
            });
        }
    });
}

/**
 * Инициализация форм
 */
function initForms() {
    const forms = document.querySelectorAll('form:not(.filter-form)');
    
    forms.forEach(form => {
        // Валидация перед отправкой
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                return false;
            }
            
            // Показываем индикатор загрузки
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner"></span> Сохранение...';
            }
            
            return true;
        });
        
        // Валидация в реальном времени
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
        
        // Маска для номера группы
        const groupInputs = form.querySelectorAll('input[name*="group"], input[id*="group"]');
        groupInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });
        });
        
        // Маска для ФИО (только буквы и дефисы)
        const nameInputs = form.querySelectorAll('input[name*="name"]');
        nameInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\- ]/g, '');
            });
        });
        
        // Ограничение года поступления
        const yearInputs = form.querySelectorAll('input[name*="year"]');
        yearInputs.forEach(input => {
            const currentYear = new Date().getFullYear();
            input.max = currentYear;
            input.min = currentYear - 50;
        });
    });
}

/**
 * Валидация формы
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Дополнительная валидация для специфичных полей
    const dateFields = form.querySelectorAll('input[type="date"]');
    dateFields.forEach(field => {
        if (field.value) {
            const date = new Date(field.value);
            const currentDate = new Date();
            
            if (date > currentDate) {
                showFieldError(field, 'Дата не может быть в будущем');
                isValid = false;
            }
        }
    });
    
    // Валидация оценок
    const gradeSelects = form.querySelectorAll('select[name="grade"]');
    gradeSelects.forEach(select => {
        if (select.value && (select.value < 2 || select.value > 5)) {
            showFieldError(select, 'Оценка должна быть от 2 до 5');
            isValid = false;
        }
    });
    
    if (!isValid) {
        showNotification('Пожалуйста, исправьте ошибки в форме', 'error');
        // Прокрутка к первой ошибке
        const firstError = form.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    return isValid;
}

/**
 * Валидация отдельного поля
 */
function validateField(field) {
    let isValid = true;
    let errorMessage = '';
    
    // Проверка на обязательность
    if (field.hasAttribute('required') && !field.value.trim()) {
        errorMessage = field.getAttribute('data-error-required') || 'Это поле обязательно для заполнения';
        isValid = false;
    }
    
    // Проверка email
    if (field.type === 'email' && field.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            errorMessage = field.getAttribute('data-error-email') || 'Введите корректный email';
            isValid = false;
        }
    }
    
    // Проверка номера телефона
    if (field.type === 'tel' && field.value) {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        if (!phoneRegex.test(field.value)) {
            errorMessage = field.getAttribute('data-error-phone') || 'Введите корректный номер телефона';
            isValid = false;
        }
    }
    
    // Проверка числового поля
    if (field.type === 'number' && field.value) {
        const min = parseFloat(field.min);
        const max = parseFloat(field.max);
        const value = parseFloat(field.value);
        
        if (!isNaN(min) && value < min) {
            errorMessage = field.getAttribute('data-error-min') || `Минимальное значение: ${min}`;
            isValid = false;
        }
        
        if (!isNaN(max) && value > max) {
            errorMessage = field.getAttribute('data-error-max') || `Максимальное значение: ${max}`;
            isValid = false;
        }
    }
    
    // Проверка даты
    if (field.type === 'date' && field.value) {
        const date = new Date(field.value);
        if (isNaN(date.getTime())) {
            errorMessage = field.getAttribute('data-error-date') || 'Введите корректную дату';
            isValid = false;
        }
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
        showFieldSuccess(field);
    }
    
    return isValid;
}

/**
 * Показать ошибку поля
 */
function showFieldError(field, message) {
    clearFieldError(field);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    
    field.parentNode.appendChild(errorDiv);
    field.classList.add('error');
    field.style.borderColor = '#e74c3c';
}

/**
 * Очистить ошибку поля
 */
function clearFieldError(field) {
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
    field.classList.remove('error');
    field.style.borderColor = '';
}

/**
 * Показать успешную валидацию поля
 */
function showFieldSuccess(field) {
    field.classList.add('success');
    field.style.borderColor = '#2ecc71';
}

/**
 * Инициализация подтверждений удаления
 */
function initConfirmations() {
    const deleteLinks = document.querySelectorAll('a[href*="delete"], .btn-danger');
    
    deleteLinks.forEach(link => {
        if (!link.closest('form')) { // Не применяем к кнопкам внутри форм подтверждения
            link.addEventListener('click', function(e) {
                if (!this.hasAttribute('data-no-confirm')) {
                    const message = this.getAttribute('data-confirm') || 
                                   'Вы уверены, что хотите выполнить это действие?';
                    
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        }
    });
}

/**
 * Инициализация выбора даты
 */
function initDatePickers() {
    // Можно подключить библиотеку для выбора даты, например flatpickr
    // flatpickr(".datepicker", {});
    
    // Простая настройка для нативных date pickers
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Устанавливаем максимальную дату как сегодня
        if (!input.max) {
            const today = new Date().toISOString().split('T')[0];
            input.max = today;
        }
        
        // Добавляем иконку календаря
        if (!input.parentNode.querySelector('.date-icon')) {
            const icon = document.createElement('span');
            icon.className = 'date-icon';
            icon.innerHTML = '📅';
            icon.style.cursor = 'pointer';
            icon.style.marginLeft = '-25px';
            icon.style.position = 'absolute';
            
            icon.addEventListener('click', function() {
                input.focus();
                input.showPicker ? input.showPicker() : input.click();
            });
            
            input.parentNode.style.position = 'relative';
            input.parentNode.appendChild(icon);
        }
    });
}

/**
 * Инициализация динамических select
 */
function initDynamicSelects() {
    // Зависимые select: дисциплины в зависимости от направления
    const directionSelects = document.querySelectorAll('select[name*="direction"], select[id*="direction"]');
    const disciplineSelects = document.querySelectorAll('select[name*="discipline"], select[id*="discipline"]');
    
    if (directionSelects.length > 0 && disciplineSelects.length > 0) {
        // Загрузка дисциплин при изменении направления
        directionSelects.forEach(select => {
            select.addEventListener('change', function() {
                updateDisciplines(this.value);
            });
            
            // Инициализация при загрузке
            if (select.value) {
                updateDisciplines(select.value);
            }
        });
    }
    
    // Динамическое обновление курса при выборе года поступления
    const admissionYearInputs = document.querySelectorAll('input[name="admission_year"]');
    const courseSelects = document.querySelectorAll('select[name="course"]');
    
    if (admissionYearInputs.length > 0 && courseSelects.length > 0) {
        admissionYearInputs.forEach(input => {
            input.addEventListener('change', function() {
                updateCourseOptions(this.value);
            });
            
            input.addEventListener('input', function() {
                updateCourseOptions(this.value);
            });
        });
    }
}

/**
 * Обновление списка дисциплин
 */
function updateDisciplines(direction) {
    if (!direction) return;
    
    const disciplineSelects = document.querySelectorAll('select[name*="discipline"], select[id*="discipline"]');
    
    // В реальном приложении здесь был бы AJAX запрос
    // Для демо просто показываем/скрываем опции
    
    disciplineSelects.forEach(select => {
        const options = select.querySelectorAll('option[data-direction]');
        let hasVisibleOptions = false;
        
        options.forEach(option => {
            if (option.getAttribute('data-direction') === direction) {
                option.style.display = '';
                hasVisibleOptions = true;
            } else {
                option.style.display = 'none';
            }
        });
        
        // Если нет видимых опций, показываем сообщение
        if (!hasVisibleOptions) {
            select.innerHTML = '<option value="">Нет доступных дисциплин для этого направления</option>';
        }
    });
}

/**
 * Обновление опций курса на основе года поступления
 */
function updateCourseOptions(admissionYear) {
    if (!admissionYear) return;
    
    const currentYear = new Date().getFullYear();
    const course = Math.min(6, currentYear - parseInt(admissionYear) + 1);
    
    const courseSelects = document.querySelectorAll('select[name="course"]');
    courseSelects.forEach(select => {
        const options = select.querySelectorAll('option');
        options.forEach(option => {
            const optionValue = parseInt(option.value);
            if (optionValue && optionValue > course) {
                option.disabled = true;
                option.style.color = '#ccc';
            } else {
                option.disabled = false;
                option.style.color = '';
            }
        });
    });
}

/**
 * Инициализация сортировки таблиц
 */
function initTableSorting() {
    const sortableHeaders = document.querySelectorAll('th[data-sortable]');
    
    sortableHeaders.forEach(header => {
        header.style.cursor = 'pointer';
        header.title = 'Нажмите для сортировки';
        
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const columnIndex = Array.from(this.parentNode.children).indexOf(this);
            const isAsc = !this.classList.contains('sort-asc');
            
            // Сбрасываем сортировку у других заголовков
            table.querySelectorAll('th[data-sortable]').forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Устанавливаем текущую сортировку
            this.classList.add(isAsc ? 'sort-asc' : 'sort-desc');
            
            // Сортируем таблицу
            sortTable(table, columnIndex, isAsc);
        });
    });
}

/**
 * Сортировка таблицы
 */
function sortTable(table, columnIndex, ascending) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aText = a.children[columnIndex].textContent.trim();
        const bText = b.children[columnIndex].textContent.trim();
        
        // Пытаемся сравнить как числа
        const aNum = parseFloat(aText.replace(',', '.'));
        const bNum = parseFloat(bText.replace(',', '.'));
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return ascending ? aNum - bNum : bNum - aNum;
        }
        
        // Иначе сравниваем как строки
        return ascending ? 
            aText.localeCompare(bText, 'ru') : 
            bText.localeCompare(aText, 'ru');
    });
    
    // Удаляем старые строки
    rows.forEach(row => tbody.removeChild(row));
    
    // Добавляем отсортированные строки
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Инициализация системы уведомлений
 */
function initNotifications() {
    // Показываем сохраненные уведомления из sessionStorage
    const savedNotification = sessionStorage.getItem('notification');
    if (savedNotification) {
        const { message, type } = JSON.parse(savedNotification);
        showNotification(message, type);
        sessionStorage.removeItem('notification');
    }
    
    // Автоматическое скрытие уведомлений через 5 секунд
    const autoHideNotifications = document.querySelectorAll('.notification[data-auto-hide]');
    autoHideNotifications.forEach(notification => {
        setTimeout(() => {
            hideNotification(notification);
        }, 5000);
    });
}

/**
 * Показать уведомление
 */
function showNotification(message, type = 'info') {
    // Создаем контейнер для уведомлений, если его нет
    let container = document.getElementById('notifications');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-message">${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Стили уведомления
    notification.style.backgroundColor = getNotificationColor(type);
    notification.style.color = 'white';
    notification.style.padding = '15px 20px';
    notification.style.marginBottom = '10px';
    notification.style.borderRadius = '5px';
    notification.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    notification.style.minWidth = '300px';
    notification.style.maxWidth = '400px';
    notification.style.transform = 'translateX(100%)';
    notification.style.transition = 'transform 0.3s ease';
    
    // Кнопка закрытия
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.background = 'none';
    closeBtn.style.border = 'none';
    closeBtn.style.color = 'white';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.fontSize = '20px';
    closeBtn.style.marginLeft = '15px';
    closeBtn.style.float = 'right';
    
    closeBtn.addEventListener('click', () => hideNotification(notification));
    
    // Добавляем в контейнер
    container.appendChild(notification);
    
    // Анимация появления
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Автоматическое скрытие
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);
    
    return notification;
}

/**
 * Получить цвет для типа уведомления
 */
function getNotificationColor(type) {
    const colors = {
        success: '#2ecc71',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    return colors[type] || colors.info;
}

/**
 * Скрыть уведомление
 */
function hideNotification(notification) {
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

/**
 * Вспомогательные функции
 */

// Форматирование даты
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    
    return date.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Форматирование номера группы
function formatGroupNumber(groupNumber) {
    return groupNumber.toUpperCase().replace(/\s+/g, '');
}

// Подсчет среднего балла
function calculateAverageGrade(grades) {
    if (!Array.isArray(grades) || grades.length === 0) return 0;
    
    const sum = grades.reduce((acc, grade) => acc + parseFloat(grade), 0);
    return (sum / grades.length).toFixed(2);
}

// Экспорт данных
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Пропускаем кнопки действий
            if (cols[j].classList.contains('actions')) continue;
            
            // Очищаем данные
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '')
                                       .replace(/(\s\s)/gm, ' ')
                                       .replace(/"/g, '""');
            
            // Оборачиваем в кавычки если есть запятые
            data = data.includes(',') ? `"${data}"` : data;
            row.push(data);
        }
        
        csv.push(row.join(','));
    }
    
    // Скачивание файла
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (navigator.msSaveBlob) {
        navigator.msSaveBlob(blob, filename);
    } else {
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// Поиск в таблице
function initTableSearch(tableId, searchInputId) {
    const searchInput = document.getElementById(searchInputId);
    const table = document.getElementById(tableId);
    
    if (!searchInput || !table) return;
    
    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

// Пагинация таблицы
function initTablePagination(tableId, itemsPerPage = 10) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    const totalPages = Math.ceil(rows.length / itemsPerPage);
    
    // Создаем элементы пагинации
    const pagination = document.createElement('div');
    pagination.className = 'pagination';
    
    for (let i = 0; i < totalPages; i++) {
        const pageLink = document.createElement('a');
        pageLink.href = '#';
        pageLink.textContent = i + 1;
        pageLink.dataset.page = i;
        
        pageLink.addEventListener('click', function(e) {
            e.preventDefault();
            showPage(parseInt(this.dataset.page));
        });
        
        pagination.appendChild(pageLink);
    }
    
    table.parentNode.insertBefore(pagination, table.nextSibling);
    
    function showPage(page) {
        const start = page * itemsPerPage;
        const end = start + itemsPerPage;
        
        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });
        
        // Обновляем активную страницу
        pagination.querySelectorAll('a').forEach(link => {
            link.classList.remove('active');
        });
        pagination.querySelector(`a[data-page="${page}"]`).classList.add('active');
    }
    
    // Показываем первую страницу
    showPage(0);
}

// Глобальный объект для доступа к функциям из консоли
window.App = {
    showNotification,
    hideNotification,
    formatDate,
    formatGroupNumber,
    calculateAverageGrade,
    exportToCSV,
    initTableSearch,
    initTablePagination
};

// Полифиллы для старых браузеров
if (!String.prototype.includes) {
    String.prototype.includes = function(search, start) {
        if (typeof start !== 'number') {
            start = 0;
        }
        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}