db = db.getSiblingDB('notifications_db');  // Use the actual DB name here
db.createCollection('notifications_log');

db = db.getSiblingDB('admin');
db.createUser({
    user: "root",
    pwd: "mo_user",
    roles: [
        { role: "userAdminAnyDatabase", db: "admin" },
        { role: "readWriteAnyDatabase", db: "notifications_db" }
    ]
});