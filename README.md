<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/yourusername/laravel-google-meet/actions"><img src="https://github.com/yourusername/laravel-google-meet/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg" alt="License"></a>
</p>

# Laravel Google Meet Scheduler

A **Laravel 10** application that allows tutors to schedule **Google Meet meetings** with students, including:

- Automatic timezone detection  
- Google Calendar integration  
- Optional download of Google Meet recordings via Google Drive  

---

## Table of Contents

1. [Features](#features)  
2. [Requirements](#requirements)  
3. [Setup Instructions](#setup-instructions)  
4. [Google API Credentials](#google-api-credentials)  
5. [Usage](#usage)  
6. [Notes](#notes)  
7. [License](#license)  

---

## Features

- Tutors can schedule meetings for students.  
- Automatically detects the user's timezone.  
- Google Meet link is generated for each meeting.  
- Attendees are invited via email.  
- Only future dates can be scheduled.  
- Optional download of Google Meet recordings (Google Workspace only).  
- Responsive UI using Bootstrap 5.  

---

## Requirements

- PHP >= 8.1  
- Laravel >= 10  
- Composer  
- MySQL or compatible database  
- Google Workspace account (for recording & Drive API)  
- Google Cloud Project with Calendar and Drive API enabled  

---

## Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/laravel-google-meet.git
cd laravel-google-meet
