DROP DATABASE IF EXISTS db_gym;
CREATE DATABASE db_gym;
USE db_gym;

-- DROP THE HIGHEST level first
 
-- TO DO
-- Change salting to NOT NULL

  DROP VIEW IF EXISTS vw_Routines; 
  DROP VIEW IF EXISTS vw_Exercises;
  DROP VIEW IF EXISTS vw_ExerciseNames;
  
  DROP TABLE IF EXISTS `tbl_routine_meta`;
  DROP TABLE IF EXISTS `tbl_exercise_values`;
  DROP TABLE IF EXISTS `tbl_routines`;
  DROP TABLE IF EXISTS `tbl_exercises`;
  DROP TABLE IF EXISTS `tbl_users`;
  DROP TABLE IF EXISTS `tbl_posts`;
  DROP TABLE IF EXISTS `tbl_comments`;
  
CREATE TABLE tbl_users(
   userId                 int(11)          AUTO_INCREMENT,                      -- The specific Id the Identfies each user
   active                 tinyint(1)       NOT NULL DEFAULT 1,                  -- Whether the account is active or not
   
   username               varchar(30)      NOT NULL,                            -- The unique username given to a particular user
   loginPassword          char(60)         NOT NULL,                            -- The password has which is stored in place of a password
   salt                   char(21),                                             -- The Salt used with the hashing function
   joined                 TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- The time which the entry was made, will determined the time they joinded
   firstName              varchar(15)      NOT NULL,                            -- Users first name
   lastName               varchar(15)      NOT NULL,                            -- Last name of the user
   emailAddress           varchar(50)      NOT NULL,                            -- Email address of the user, may soon be used to login
   adminLevel             tinyint(1)       NOT NULL DEFAULT 0,                  -- Whether they have administator privledges or not
   deactivationTime       DATE,                                                 -- If the active is active or not
   lastTimeActive         TIMESTAMP        NOT NULL,                            -- Last time the user has logged in or done something
   help                   tinyint(1)       NOT NULL DEFAULT 1,                  -- Has the help flag been set?
   isKilo                 tinyint(1)       NOT NULL,                            -- View weight in Kilos?
   registeredIP           char(45),                                             -- The IP the user registed on
   timeZone               varchar(64),                                          -- What timezone the user is in
   registeredEmail        tinyint(1)       NOT NULL DEFAULT 0,                  -- Has the email been registered?
   regEmailSentTime       TIMESTAMP,                                            -- Last time a registraion email has been sent to the account
   recovEmailSentTime     TIMESTAMP,

   UNIQUE(username),
   UNIQUE(emailAddress),
   Primary Key(userId)
);
  
  
CREATE TABLE tbl_exercises(
   exerciseId         int(11)        AUTO_INCREMENT,                       -- The exercise Id given to an exercise, this is unique to each user
   active             tinyint(1)     NOT NULL DEFAULT 1,                   -- Whether the record has been deleted or not
   userIdF            int(11)        NOT NULL,                             -- The User who owns the record
   
   name               varchar(30)    NOT NULL,                             -- The user given name of the exercise
   description        varchar(256)   NOT NULL,                             -- A small description of the exercise
   reps               int(11)        NOT NULL,                             -- Amount of repitions recommended for each exercise
   sets               int(11)        NOT NULL,                             -- Amount of sets recommeded for each exercise
   muscleGroup        varchar(256),                                        -- Particular muscle group the exercise targets
   timeCreated        TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP, 
   deactivationTime   DATE,                                                -- What date the record has be deactived at
   rating             float(4,1),                                          -- Rating between 00.0 and 100.0 
   mediaUrl           varchar(256),                                        -- Stored as NULL(NO VIDEO) or a value, video link to youtube. 

   -- IS UNIQUE TO ADMIN
   share              tinyint(1)     NOT NULL,                             -- Allows the exercise to be seen in the database                                    
   defaultExercise    tinyint(1),                                          -- Is an exercise that's included by default when the user signs up.

   

   UNIQUE(name, userIdF),                                                  -- a user can't own an exercise with the same name. 
   Primary Key(exerciseId),
   FOREIGN KEY(userIdF) REFERENCES tbl_users(userId)
);


CREATE TABLE tbl_routines(
   routineId         int(11)           AUTO_INCREMENT,                      -- The Primary ID for the routine collection
   active            tinyint(1)        NOT NULL DEFAULT 1,                  -- Whether the record has been deleted or not
   userIdF           int(11)           NOT NULL,                            -- The User who owns the record
   exerciseIdF       int(11)           NOT NULL,                            -- Determines which exercise is linked in
   
   routineName       varchar(30)       NOT NULL,                            -- Name of the routine
   timeCreated       TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP, 
   deactivationTime  DATE,                                                  -- What date the record has be deactived at


   -- IS UNIQUE TO ADMIN
   share              tinyint(1)     NOT NULL,                             -- Allows the routine to be seen in the database                                    
   defaultRoutine     tinyint(1),                                          -- Is a routine that's included by default when the user signs up.
   
   UNIQUE(userIdF, exerciseIdF, routineName),                               -- a routine can't have the same exercise, in the routine.
   Primary Key(routineId),                                                  -- Primary key, this is used for indexing
   FOREIGN KEY(exerciseIdF) REFERENCES tbl_exercises(exerciseId),           -- This is used to enforce refeential integrity
   FOREIGN KEY(userIdF) REFERENCES tbl_users(userId)
);

CREATE TABLE  tbl_routine_meta(
  routineMetaId       int(11)       AUTO_INCREMENT,                         -- Priamry key of the table
  routineNameF        varchar(30)   NOT NULL,                               -- The name that references the collection of routines
  userIdF             int(11)       NOT NULL,                               -- The user who owns the collection

  routineNotes        text          NOT NULL,                               -- Information on the routine
  purpose             text          NOT NULL,                               -- Why you would do this routine
  timeCreated         TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,     -- When the collection was created
  deactivationTime    DATE,                                                 -- When the collection was declated deleted
  rating              float(4,1),  

  UNIQUE(routineNameF, userIdF),   
  -- FOREIGN KEY(routineNameF) REFERENCES tbl_routines(routineName),
  FOREIGN KEY(userIdF) REFERENCES tbl_users(userId),
  Primary Key(routineMetaId)
);

CREATE TABLE tbl_exercise_values(
  liftId             int(11)               AUTO_INCREMENT, -- The into record value(primary key)
  active             tinyint(1)            NOT NULL DEFAULT 1,
  userIdF            int(11)               NOT NULL,
  
  weightDone         float(7,3)            NOT NULL,       -- The amount 
  repsCompleted      int(11)               NOT NULL,
  setsCompleted      int(11)               NOT NULL,
  timeCompleted      TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
  exerciseIdF        int(11)               NOT NULL,
  notes              text                  NOT NULL,      -- EMPTY MEANS NO COMMENT, Text means there is a comment
  deactivationTime   DATE, 
  
  Primary KEY(liftId),
  FOREIGN KEY(exerciseIdF) REFERENCES tbl_exercises(exerciseId),
  FOREIGN KEY(userIdF) REFERENCES tbl_users(userId)
);


-- 
-- Comments and post tables
-- 

CREATE TABLE tbl_posts(
  postId       int(11)       AUTO_INCREMENT,
  title        varchar(256)  NOT NULL,
  bodyText     text          NOT NULL,
  previewText  text          NOT NULL,

  author       int(11)       NOT NULL,
  tags         varchar(256),
  creationDate TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  editedBy     int(11),
  editDate     datetime,
  isEdit       tinyint(1)    NOT NULL DEFAULT 0,
  
  UNIQUE(title),
  FOREIGN KEY(author) REFERENCES tbl_users(userId),
  FOREIGN KEY(editedBy) REFERENCES tbl_users(userId),
  Primary Key(postId)
);

CREATE TABLE tbl_comments(
  commentId         int(11)      AUTO_INCREMENT,
  userIdF           int(11)      NOT NULL,
  postIdF           int(11)      NOT NULL,
  active            tinyint(1)   NOT NULL DEFAULT 1,

  title             varchar(256) NOT NULL,
  bodyText          text         NOT NULL,
  creationDate      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deactivationTime  DATE, 

  FOREIGN KEY(userIdF) REFERENCES tbl_users(userId),
  FOREIGN KEY(postIdF) REFERENCES tbl_posts(postId),
  PRIMARY KEY(commentId)
);

Create view vw_Routines AS 
   SELECT 
       tbl_routines.routineId   as   routineId,
       tbl_exercises.name       as   exerciseName,
       tbl_routines.routineName as   routineName, 
       tbl_exercises.exerciseId as   exerciseId,
       tbl_routines.userIdF     as   userIdF,
       tbl_routines.active      as   routineActive,
       tbl_exercises.active     as   exercisesActive
       FROM tbl_routines INNER JOIN tbl_exercises
       ON tbl_routines.exerciseIdF = tbl_exercises.exerciseId
       ORDER BY tbl_routines.routineId;

-- Joins where exercideIdF = exercideId on, exercises and exercise_values 
Create view vw_ExerciseValues AS 
   SELECT 
       tbl_exercise_values.setsCompleted      as   setsCompleted,
       tbl_exercise_values.repsCompleted      as   repsCompleted,
       tbl_exercise_values.timeCompleted      as   timeCompleted,
       tbl_exercise_values.active             as   active,
       tbl_exercise_values.weightDone         as   weight,
       tbl_exercise_values.liftId             as   liftId,
       tbl_exercise_values.notes              as   inputDescription, 
       tbl_exercise_values.userIdF            as   userIdF, 
       tbl_exercises.name                     as   exerciseName,
       tbl_exercises.sets                     as   setsRecommened,
       tbl_exercises.reps                     as   repsRecommened,
       tbl_exercise_values.exerciseIdF        as   exerciseId
       
       FROM 
          tbl_exercise_values INNER JOIN tbl_exercises
       ON 
          tbl_exercise_values.exerciseIdF = tbl_exercises.exerciseId;  

-- Returns the exercise names, using the exercise foreign key in the table routine
Create view vw_Exercises AS
    SELECT 
      tbl_exercise_values.liftId as liftId,
      tbl_exercise_values.weightDone as weightDone,
      tbl_exercises.name as name,
      tbl_exercise_values.notes as notes,
      tbl_exercise_values.userIdF as userIdF
        FROM
          tbl_exercise_values 
        INNER JOIN
          tbl_exercises
      ON tbl_exercises.exerciseId = tbl_exercise_values.exerciseIdf;     

-- Returns the exercise name for a given routine
CREATE view vw_ExerciseNames AS
SELECT 
    tbl_exercises.name        as exerciseName,
    tbl_exercises.exerciseId  as exerciseId,
    tbl_routines.routineName  as routineName,
    tbl_routines.userIdF      as userIdF,
    tbl_exercises.active      as exerciseActive,
    tbl_routines.active       as routineActive
      FROM 
        tbl_routines 
      INNER JOIN 
        tbl_exercises
      ON tbl_routines.exerciseIdF = tbl_exercises.exerciseId; 
--      WHERE routineName = 'FIRST ROUTINE';  -- To input on the view

INSERT INTO tbl_users(username, loginPassword, joined, firstName, lastName, emailAddress, adminLevel, isKilo, lastTimeActive, salt) VALUES("compulsed", "$2a$05$1MCxqzryxGEFhec47Ycol.nYm1TJ7SYsbbuenz42AdjFUGIzVGHtG", now(), "Dale", "Salter", "djsalter93@hotmail.com", 1, 1, now(), "1MCxqzryxGEFhec47Ycol");
INSERT INTO tbl_users(username, loginPassword, joined, firstName, lastName, emailAddress, adminLevel, isKilo, lastTimeActive, salt) VALUES("wilburfore", "$2a$05$1MCxqzryxGEFhec47Ycol.nYm1TJ7SYsbbuenz42AdjFUGIzVGHtG", now(), "Simon", "Babb", "simon@live.com", 1, 1, now(), "1MCxqzryxGEFhec47Ycol");
INSERT INTO tbl_users(username, loginPassword, joined, firstName, lastName, emailAddress, adminLevel, isKilo, lastTimeActive, salt) VALUES("markus", "$2a$05$1MCxqzryxGEFhec47Ycol.nYm1TJ7SYsbbuenz42AdjFUGIzVGHtG", now(), "Mark", "Flately", "mark@hotmail.com", 0, 0, now(), "1MCxqzryxGEFhec47Ycol");

UPDATE tbl_users SET registeredEmail = TRUE, help = FALSE WHERE userId = 1;

-- Inserting some exercises
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Bench", 1, 4, "This is some information1", "Chest", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Squat", 2, 5, "This is some information2", "Hamstrings", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Curl", 3, 6, "This is some information3", "Biceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Press", 4, 6, "This is some information4", "Biceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Pushup", 5, 6, "This is some information5", "Shoulders", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Laterial", 6, 6, "This is some information6", "Lats", NULL, 1);

INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Back Row", 1, 4, "This is some information1", "Quadriceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Leg Press", 2, 5, "This is some information2", "Hamstrings", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Sit up", 3, 6, "This is some information3", "Biceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Running", 4, 6, "This is some information4", "Neck", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "777", 5, 6, "This is some information5", "Biceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "Decline Bench", 6, 6, "This is some information6", "Lats", NULL, 1);

INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 0, "Jogging", 1, 1, "Information on jogging", "Biceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 0, "Swimming", 1, 1, "Some information on swimming", "Biceps", NULL, 1);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "FLYING", 1, 1, "Information on jogging", "Biceps", NULL, 0);
INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup, mediaUrl, defaultExercise) VALUES(1, 1, "JUMPING", 1, 1, "Information on jogging", "Biceps", NULL, 0);


-- INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup) VALUES(1, 0, "BLAH 13", 6, 6, "This is some information6", "Lats, Hamstrings", 0);
-- INSERT INTO tbl_exercises(userIdF, share, name, sets, reps, description, muscleGroup) VALUES(1, 0, "WEEE 14", 6, 6, "This is some information6", "Lats, Triceps", 0);


-- Inserting some routines
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 1, "MONDAY", 1, 1); -- Inserts Bench
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 2, "MONDAY", 1, 1); -- Inserts Curl
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 4, "MONDAY", 1, 1); -- Inserts Curl
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 5, "MONDAY", 1, 1); -- Inserts Bench
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 6, "MONDAY", 1, 1); -- Inserts Curl
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 7, "MONDAY", 1, 1); -- Inserts Curl
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 8, "MONDAY", 1, 1); -- Inserts Bench
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 9, "MONDAY", 1, 1); -- Inserts Curl
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 10, "MONDAY", 1, 1); -- Inserts Curl

INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 1, "TUESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 3, "TUESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 2, "TUESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 5, "TUESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 6, "TUESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 2, "WEDNESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 5, "WEDNESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 6, "WEDNESDAY", 1, 1);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 2, "Weekend", 0, 0);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 5, "Weekend", 0, 0);
INSERT INTO tbl_routines(userIdF, exerciseIdF, routineName, share, defaultRoutine) VALUES(1, 6, "Weekend", 0, 0);


INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1 ,25.5, 5, 4, now(), 1, "Good day");
INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1, 50.2, 5, 4, now(), 2, "Bad ex");
INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1, 55.2, 5, 4, now(), 2, "Fun, but like this");
INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1, 65.2, 5, 4, now(), 2, "Try it like this next time");
INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1, 80.5, 5, 4, now(), 3, "Nothing");
INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1, 10.5, 5, 4, now(), 4, "");
INSERT INTO tbl_exercise_values(userIdF, weightDone, repsCompleted, setsCompleted, timeCompleted, exerciseIdF, notes) VALUES(1, 10.5, 5, 4, now(), 4, "Something");


 INSERT INTO tbl_posts(`title`, `previewText` ,`bodyText`, `author`) VALUES('Welcome to my Website', 'Welcome to my pre-alpha version of my website, feel free to browse around, register an account and try out the application.<br><br>No information will be saved, but you\'ll be able to provide much needed text experience for the application.<br><br>Thanks!<br>~Complused', 'This is the first post on my pesudo website', 1);
 -- INSERT INTO tbl_posts(`title`, `previewText` ,`bodyText`, `author`) VALUES('WELCOME ALL2!', 'This is what you see on the font page', 'This is the first post on my pesudo website', 1);
 -- INSERT INTO tbl_posts(`title`, `previewText` ,`bodyText`, `author`) VALUES('WELCOME ALL3!', 'This is what you see on the font page', 'This is the first post on my pesudo website', 1);
 -- INSERT INTO tbl_posts(`title`, `previewText` ,`bodyText`, `author`) VALUES('WELCOME ALL4!', 'This is what you see on the font page', 'This is the first post on my pesudo website', 1);
 -- INSERT INTO tbl_posts(`title`, `previewText` ,`bodyText`, `author`) VALUES('WELCOME ALL5!', 'This is what you see on the font page', 'This is the first post on my pesudo website', 1);
 -- INSERT INTO tbl_posts(`title`, `previewText` ,`bodyText`, `author`) VALUES('WELCOME ALL6!', 'This is what you see on the font page', 'This is the first post on my pesudo website', 1);


 INSERT INTO tbl_comments(title, bodyText, userIdF, postIdF) VALUES('FIRST COMMENT', "BLAH BLAH", 1, 1);
-- INSERT INTO tbl_posts(`title`, `bodyText`, `author`) VALUES('THIS IS SOME TITLE TEXT', 'BODY TEXT', 1);
-- INSERT INTO tbl_posts(`title`, `bodyText`, `author`) VALUES('THIS IS SOME TITLE TEXT', 'BODY TEXT', 1);
-- INSERT INTO tbl_posts(`title`, `bodyText`, `author`) VALUES('THIS IS SOME TITLE TEXT', 'BODY TEXT', 1);


-- DELIMITER //

-- CREATE PROCEDURE `p2` ()
-- LANGUAGE SQL
-- DETERMINISTIC
-- SQL SECURITY DEFINER
-- COMMENT 'A procedure'
-- BEGIN
--   INSERT INTO tbl_posts(`title`, `bodyText`, `author`) VALUES('THIS IS SOME TITLE TEXT', 'BODY TEXT', 1);
-- END//

-- CALL `p2`();
-- CALL `p2`();
-- CALL `p2`();


-- CREATE PROCEDURE `pPosts` (IN var1 varchar(256))
-- LANGUAGE SQL
-- DETERMINISTIC
-- SQL SECURITY DEFINER
-- COMMENT 'A procedure'
-- BEGIN
--   INSERT INTO tbl_posts(`title`, `bodyText`, `author`) VALUES(var1, 'BODY TEXT', 1);
-- END//

-- CALL `pPosts`("This is a string");