# Sendy Rolling Timestamps

This script will allow you to enable "rolling" timestamps in your segments. According to how you set up your cron job, any segment you designate will increment its timestamp value(s) by 1 day.

For example, today is 2/8/2024. A segment called "Last Active Today: ROLLING" today has the condition: [Last activity] [on] **[Thur Feb 08 2024]**

Tomorrow, it will have the condition: [Last activity] [on] **[Fri Feb 09 2024]**

Another example: Today is 2/8/2024. A segment called "Active Last 30 Days: ROLLING" has the condition: [Last activity] [between] **[Tue Jan 09 2024] AND [Thur Feb 08 2024]**

Tomorrow, it will have the condition: [Last activity] [between] **[Wed Jan 10 2024] AND [Fri Feb 09 2024]**

Any segment that contains timestamp conditions, and "ROLLING" in the segment name, will increment by one day when the script is executed.

## Getting Started

Upload rolling_timestamps.php to your server. I created a "Scripts" directory next to my public_html.

Next, modify this script on line 8 (require_once '/path/to/your/sendy/includes/config.php';) to reflect the location of your sendy/includes/config.php. The script automatically connects to your database by including your sendy/includes/config.php, so as long as you modify this line, and Sendy can connect to your database, this script should as well.

Next, create a cron job, similar to how you created your Sendy crons:

This is the schedule I use: 0 1 * * * to update once each day at 1:00AM.

This is the command: php /path/to/rolling_timestamps.php > /dev/null 2>&1

Of course, you will need to adjust the /path/to/rolling_timestamps.php depending on where you put this script.

### Usage
To create a segment with rolling timestamps, just include the word "ROLLING" in your segment name. "Active in Past 30 Days" will **not** increment, but "Active in Past 30 Days: ROLLING" **will** increment.

#### Prerequisites

-Sendy install

-Ability to upload file and set crons

##### Credit
I used ChatGPT for help w/ this script. Improvements or suggestions welcome.

Thank you Ben for Sendy
