# ETL процесс для инициализации базы данных movies_rating.db
echo "Starting database initialization process..."

# Запускаем PowerShell скрипт для генерации SQL
echo "Generating SQL script using PowerShell..."
$POWERSHELL_CMD -ExecutionPolicy Bypass -File "make_db_init.ps1"

if [ $? -ne 0 ]; then
    echo "Error: SQL script generation failed!"
fi

# Загружаем SQL скрипт в базу данных
echo "Loading SQL script into database..."
sqlite3 movies_rating.db < db_init.sql

if [ $? -eq 0 ]; then
    echo "Database initialization completed successfully!"
    echo "Database file: movies_rating.db"
    
    # Проверяем созданные таблицы
    echo "Created tables:"
    sqlite3 movies_rating.db ".tables"
else
    echo "Error: Database initialization failed!"
fi
