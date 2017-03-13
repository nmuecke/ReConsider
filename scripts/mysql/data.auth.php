-- scripts/mysql/data.auth.sql


INSERT INTO auth ( username, real_name, password, password_salt ) 
          VALUES ( 'test', 'test user', '', '' );
