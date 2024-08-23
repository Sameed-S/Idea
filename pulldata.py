import requests
import sqlite3
import os

# Function to create a connection to the SQLite database
def create_connection(db_file):
    """ Create a database connection to the SQLite database specified by db_file. """
    conn = None
    try:
        conn = sqlite3.connect(db_file)
    except sqlite3.Error as e:
        print(e)
    return conn

# Function to create the meals table if it doesn't exist
def create_table(conn):
    """ Create the meals table in the database if it doesn't exist. """
    try:
        sql_create_meals_table = """ CREATE TABLE IF NOT EXISTS meals (
                                        idMeal TEXT PRIMARY KEY,
                                        strMeal TEXT NOT NULL,
                                        strCategory TEXT,
                                        strArea TEXT,
                                        strInstructions TEXT,
                                        strMealThumb TEXT,
                                        strTags TEXT,
                                        strYoutube TEXT
                                    ); """
        cursor = conn.cursor()
        cursor.execute(sql_create_meals_table)
    except sqlite3.Error as e:
        print(e)

# Function to insert meal data into the database
def insert_meal(conn, meal):
    """ Insert a meal into the meals table. """
    sql = ''' INSERT OR REPLACE INTO meals(idMeal, strMeal, strCategory, strArea, strInstructions, strMealThumb, strTags, strYoutube)
              VALUES(?, ?, ?, ?, ?, ?, ?, ?) '''
    cursor = conn.cursor()
    cursor.execute(sql, (meal['idMeal'], meal['strMeal'], meal['strCategory'], 
                         meal['strArea'], meal['strInstructions'], 
                         meal['strMealThumb'], meal['strTags'], meal['strYoutube']))
    conn.commit()
    return cursor.lastrowid

def get_all_meals():
    base_url = "https://www.themealdb.com/api/json/v1/1/search.php?f="
    all_meals = []

    # Iterate over each letter in the alphabet to get all meals
    for letter in range(ord('a'), ord('z') + 1):
        api_url = base_url + chr(letter)
        response = requests.get(api_url)
        data = response.json()
        
        if data.get("meals"):  # Check if there are meals for this letter
            all_meals.extend(data["meals"])

    return all_meals

# Directory to store the SQLite database
db_file = os.path.join(".db")

# Create a database connection
conn = create_connection(db_file)

# Create the meals table if it doesn't exist
if conn is not None:
    create_table(conn)

# Fetch all meals and store them in the SQLite database
all_meals_data = get_all_meals()
for meal in all_meals_data:
    insert_meal(conn, meal)

# Print the total number of meals and the first meal as an example
print(f"Total meals fetched: {len(all_meals_data)}")
if all_meals_data:
    print(all_meals_data[0])

# Close the database connection
conn.close()
