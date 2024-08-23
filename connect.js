const mysql = require('mysql2');

const db = mysql.createConnection({
    host: 'sql1.njit.edu',      // IP address or domain of your remote MySQL server
    port: 3306,                 // The port number MySQL is listening on (default is 3306)
    user: '',        // Your MySQL username for the remote server
    password: '', // Your MySQL password for the remote server
    database: ''      // Your MySQL database name on the remote server
});

db.connect((err) => {
    if (err) {
        console.error('Error connecting to the database:', err);
        return;
    }
    console.log('Connected to the database');
});

// Optional: Close the connection after use
db.end();
