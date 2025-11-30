CREATE TABLE `programme` (
    `program_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `pgm_code` VARCHAR(255) NOT NULL,
    `pgm_name` VARCHAR(255) NOT NULL,
    `dept_name` VARCHAR(255) NOT NULL
);

CREATE TABLE `courses` (
    `course_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `course_code` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `is_elective` BOOLEAN NOT NULL,
    `programme` VARCHAR(255) NOT NULL,
    `sem` INT NOT NULL,
    `department` VARCHAR(255) NOT NULL,
    UNIQUE (`course_code`)
);

CREATE TABLE `exam_definition` (
    `exam_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `scheme` VARCHAR(255) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL
);

CREATE TABLE `exam_slots` (
    `slot_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `scheme` VARCHAR(255) NOT NULL,
    `slot_name` VARCHAR(255) NOT NULL,
    `course_code` VARCHAR(255) NOT NULL,
    `branch` VARCHAR(255) NOT NULL,
    `semester` INT NOT NULL,
    UNIQUE (`course_code`),
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`course_code`) REFERENCES `courses`(`course_code`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `exam_schedule` (
    `schedule_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `slot_name` VARCHAR(255) NOT NULL,
    `exam_date` DATE NOT NULL,
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `faculty` (
    `faculty_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `max_duties` INT NOT NULL,
    `total_duties` INT NOT NULL,
    `balance_duties` INT NOT NULL
);

CREATE TABLE `students` (
    `student_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `reg_no` VARCHAR(255) NOT NULL,
    `rollno` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `branch` VARCHAR(255) NOT NULL,
    `semester` INT NOT NULL,
    `exam_id` INT UNSIGNED NOT NULL,
    `elective_1` VARCHAR(255) NULL,
    `elective_2` VARCHAR(255) NULL,
    `elective_3` VARCHAR(255) NULL,
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `student_slot_registrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `exam_id` INT UNSIGNED NOT NULL,
    `slot_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`slot_id`) REFERENCES `exam_slots`(`slot_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `rooms` (
    `room_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `room_name` VARCHAR(255) NOT NULL,
    `room_type` VARCHAR(255) NOT NULL,
    `capacity` INT NOT NULL,
    `is_Available` BOOLEAN NOT NULL,
    `block` VARCHAR(255) NOT NULL,
    `floor` INT NOT NULL,
    UNIQUE (`room_name`)
);

CREATE TABLE `room_allocations` (
    `alloc_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `room_id` INT UNSIGNED NOT NULL,
    `slot_id` INT UNSIGNED NOT NULL,
    `student_id` INT UNSIGNED NOT NULL,
    `desk_no` INT NOT NULL,
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`slot_id`) REFERENCES `exam_slots`(`slot_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `exam_timetable` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_date` DATE NOT NULL,
    `session` VARCHAR(255) NOT NULL,
    `course_id` BIGINT UNSIGNED NOT NULL,
    `semester` INT NOT NULL,
    `dept` VARCHAR(255) NOT NULL,
    `program_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`program_id`) REFERENCES `programme`(`program_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `invigilation_assignments` (
    `assign_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `room_id` INT UNSIGNED NOT NULL,
    `faculty_id` INT UNSIGNED NOT NULL,
    `schedule_id` INT UNSIGNED NOT NULL,
    `duty_date` DATE NOT NULL,
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`room_id`) REFERENCES `rooms`(`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`faculty_id`) REFERENCES `faculty`(`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`schedule_id`) REFERENCES `exam_schedule`(`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `duty_summary` (
    `duty_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `exam_id` INT UNSIGNED NOT NULL,
    `faculty_id` INT UNSIGNED NOT NULL,
    `duties` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`exam_id`) REFERENCES `exam_definition`(`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`faculty_id`) REFERENCES `faculty`(`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
);
