**Daily Points Reward:** Users receive a configurable number of points once per active day.

**Ceiling Limit:** Admins can define a maximum cumulative point limit for users via plugin settings to prevent abuse.

**Performance Optimization:** To reduce unnecessary database queries, a browser cookie is used to track whether a user has already received points for the current day.

**Tamper-Resilient Design:** Although the cookie can be manually altered by the user, such tampering merely triggers a fallback database check. This ensures data integrity while maintaining low server load in normal usage.

**Profile Chart Integration:**
Earned points from daily activity are automatically reflected in the user’s profile chart, giving a visual representation of active participation and engagement trends.

**Automatic Recalculation:**
Any change in the point-per-day or maximum ceiling settings triggers automatic recalculation of points, ensuring the point totals stay consistent with updated configurations.



