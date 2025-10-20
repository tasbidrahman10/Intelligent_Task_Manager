# Intelligent_Task_Manager
🧠 Intelligent Task Manager (Rule-Based Smart Scheduler)
📋 Overview

Intelligent Task Manager is a web-based productivity application designed to help students organize, schedule, and track their academic and personal tasks.
It uses a rule-based scheduling engine to suggest optimal task schedules based on task priority, deadlines, and user-defined available time.

Users can:

Register and log in securely

Create, edit, delete, and track tasks

Automatically receive smart scheduling suggestions

View and manage schedules through an interactive dashboard

This project was developed as part of CSE327: Software Engineering (Summer 2025) under the Software Development Life Cycle (SDLC) framework.
🚀 Key Features

✅ User Authentication

Secure registration and login using PHP sessions

Password encryption and session management

✅ Task Management

Add, edit, delete, and view tasks

Toggle task status between pending and completed

Prioritize tasks by urgency or deadline

✅ Smart Scheduling Engine

Implements rule-based logic to recommend task schedules

Considers user’s available time, task priority, and due dates

✅ Admin Panel

Admin can manage users and oversee all tasks

Separate authentication for admin users

✅ Dashboard & Statistics

Displays user tasks and performance metrics

Visual breakdown of completed vs pending tasks

✅ Database Integration

MySQL database with relational design for users, tasks, and schedules

🏗️ System Architecture

Frontend: HTML, CSS, JavaScript (Bootstrap for UI styling)

Backend: PHP (Core business logic and rule-based scheduler)

Database: MySQL

🧠 Rule-Based Scheduling Logic

The scheduling engine in schedule_engine.php applies predefined conditions to recommend optimal task times:

Tasks with earlier deadlines get higher priority

If tasks have the same deadline, shorter tasks are preferred first

Completed tasks are excluded automatically

Schedule avoids overlapping time blocks

🧪 Testing

The application was tested for:

User authentication errors

Task CRUD functionality

Schedule overlaps

UI responsiveness and usability

💡 Future Enhancements

Integration with Google Calendar

AI-based scheduler using ML for personalized recommendations

Mobile-friendly responsive UI

Pomodoro Timer

Daily/Weekly Analytics Visualization

Scheduling Engine: Rule-based logic defined in schedule_engine.php

Server: XAMPP / Apache
