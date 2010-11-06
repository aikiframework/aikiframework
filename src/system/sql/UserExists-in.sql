
SELECT IF(user.user='@DB_USER@','TRUE','')
AS USER_EXISTS
FROM mysql.user
WHERE user.user='@DB_USER@'\G
