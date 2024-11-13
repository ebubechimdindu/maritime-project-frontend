   markdown  Maritime Incident Web Application Portal



   Table of Contents
- [Introduction]( introduction)
- [Features]( features)
- [Stakeholders]( stakeholders)
- [Technology Stack]( technology-stack)
- [Database Structure]( database-structure)
- [Usage]( usage)
- [Contributing]( contributing)

   Introduction

The   Maritime Incident Web Application Portal   is a comprehensive platform designed to facilitate the reporting, management, and tracking of maritime incidents. This portal serves various stakeholders, including fire services, maritime police, first responders, ship captains, and more, providing a centralized system to enhance coordination and response during maritime emergencies.

   Features

-   User Authentication  
  - Secure sign-up and login with role-based access control
  - Password management and recovery

-   User Roles and Permissions  
  - Admin, Fire Service Personnel, Maritime Police, First Responders, Ship Captains, Data Analysts

-   Ship Information Management  
  - Register and manage detailed ship information including ship ID, name, origin, destination, cargo, and more

-   Incident Reporting and Management  
  - Submit and categorize incidents with detailed descriptions, severity levels, and attachments
  - Assign incidents to relevant responders

-   Alert and Notification System  
  - Real-time alerts via email, SMS, and in-app notifications
  - Customizable alert preferences and escalation procedures

-   Incident Status Tracking  
  - Monitor the progress of incident resolution with status updates and historical logs
  - Dashboard view for a quick overview of all incidents

-   Emergency Service Request Handling  
  - Request specific emergency services and allocate resources efficiently
  - Track the status of service requests from initiation to resolution

-   Data Collection, Storage, and Retrieval  
  - Structured data entry forms and secure database storage
  - Advanced search, filter, and export options for data management

-   Data Analytics and Compliance Auditing  
  - Analytics dashboards and reporting tools for incident trends and performance metrics
  - Compliance logs and audit trails for regulatory adherence

-   Weather and Environmental Data Integration  
  - Real-time weather data integration and environmental monitoring
  - Predictive alerts based on weather forecasts

-   Emergency Service Integration  
  - Integration with external services like fire departments, coast guards, and hospitals
  - Automated dispatch and communication tools

   Stakeholders

-   Primary Stakeholders  
  - Fire Services
  - Maritime Police
  - First Responders
  - Ship Captains
  - Emergency Service Coordinators
  - System Administrators

-   Additional Potential Users  
  - Port Authorities
  - Environmental Agencies
  - Insurance Companies
  - Logistics Managers
  - Data Analysts

   Technology Stack

-   Frontend  
  - HTML5
  - CSS3
  - JavaScript
  - Bootstrap (for responsive design)

-   Backend  
  - PHP
  - MySQL

-   Tools and Libraries  
  - jQuery (optional)
  - PHP Libraries for authentication and notifications
  - OpenWeatherMap API for weather data integration

   Database Structure

The application uses a MySQL database with the following key tables:

1.   Users  
   -  user_id ,  username ,  email ,  password_hash ,  role_id ,  created_at ,  updated_at 

2.   Roles  
   -  role_id ,  role_name ,  description 

3.   Ships  
   -  ship_id ,  ship_name ,  imo_number ,  flag_state ,  vessel_type ,  capacity ,  arriving_from ,  destination ,  content ,  created_at ,  updated_at 

4.   Incidents  
   -  incident_id ,  reported_by ,  ship_id ,  incident_type ,  description ,  severity_level ,  location ,  date_time ,  status ,  attachments ,  created_at ,  updated_at 

5.   Notifications  
   -  notification_id ,  incident_id ,  user_id ,  message ,  type ,  is_read ,  created_at 

6.   Services  
   -  service_id ,  service_name ,  contact_info ,  availability ,  created_at ,  updated_at 

7.   WeatherData  
   -  weather_id ,  location ,  temperature ,  humidity ,  wind_speed ,  wind_direction ,  weather_condition ,  timestamp 

8.   ComplianceLogs  
   -  log_id ,  incident_id ,  user_id ,  action ,  timestamp 

   Installation

    Prerequisites

-   Web Server  : Apache or Nginx
-   PHP  : Version 7.4 or higher
-   MySQL  : Version 5.7 or higher
-   Composer  : For dependency management (optional)

    Steps

1.   Clone the Repository  
      bash
   git clone https://github.com/yourusername/maritime-incident-portal.git
   cd maritime-incident-portal
      

2.   Set Up the Database  
   - Create a new MySQL database.
   - Import the provided  database.sql  file to set up the necessary tables.
        bash
     mysql -u yourusername -p maritime_incident < database.sql
        

3.   Configure Environment Variables  
   - Rename  .env.example  to  .env  and update the database credentials.
        env
     DB_HOST=localhost
     DB_USERNAME=yourusername
     DB_PASSWORD=yourpassword
     DB_DATABASE=maritime_incident
        

4.   Install Dependencies  
   - If using Composer:
        bash
     composer install
        

5.   Set Up the Web Server  
   - Configure your web server to serve the project directory.
   - Ensure the  public  directory is set as the root.

6.   Run the Application  
   - Access the application via  http://localhost/maritime-incident-portal  in your web browser.

   Usage

1.   Register an Account  
   - Navigate to the sign-up page and create a new account.

2.   Login  
   - Use your credentials to log in to the portal.

3.   Dashboard  
   - View recent incidents, alerts, and notifications.

4.   Manage Ships  
   - Add, edit, or delete ship information.

5.   Report Incidents  
   - Submit detailed reports on maritime incidents.

6.   Manage Incidents  
   - View, assign, and track the status of incidents.

7.   Notifications  
   - Receive real-time alerts and manage notification preferences.

8.   Analytics  
   - Access dashboards and generate reports on incident data.

   

Contributions are welcome!     Notes:
