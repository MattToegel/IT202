# Project Name: (Which Project)
## Project Summary: (Copy from proposal)
## Github Link: (Prod Branch of Project Folder)
## Project Board Link: 
## Website Link: (Heroku Prod of Project folder)
## Your Name:

<!-- Line item / Feature template (use this for each bullet point) -- DO NOT DELETE THIS SECTION


- [ ] \(mm/dd/yyyy of completion) Feature Title (from the proposal bullet point, if it's a sub-point indent it properly)
  -  Link to related .md file: [Link Name](link url)

 End Line item / Feature Template -- DO NOT DELETE THIS SECTION --> 
 
 
### Proposal Checklist and Evidence

- Milestone 1
    - [x] \(03/05/2022) User will be able to register a new account
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/register.php](https://mt85-prod.herokuapp.com/Project/register.php)
    - [x] \(03/05/2022) User will be able to login to their account (given they enter the correct credentials)
        -  [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/login.php](https://mt85-prod.herokuapp.com/Project/login.php)
    - [x] \(03/05/2022) User will be able to logout
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/logout.php](https://mt85-prod.herokuapp.com/Project/logout.php)
    - [ ] \(mm/dd/yyyy of completion) Basic security rules implemented
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/profile.php](https://mt85-prod.herokuapp.com/Project/profile.php)
    - [ ] \(mm/dd/yyyy of completion) Basic Roles implemented
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/admin/create_role.php](https://mt85-prod.herokuapp.com/Project/admin/create_role.php)
    - [ ] \(mm/dd/yyyy of completion) Site should have basic styles/theme applied; everything should be styled
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/home.php](https://mt85-prod.herokuapp.com/Project/home.php)
        - [https://mt85-prod.herokuapp.com/Project/styles.css](https://mt85-prod.herokuapp.com/Project/styles.css)
    - [ ] \(mm/dd/yyyy of completion) Any output messages/errors should be “user friendly”
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/logout.php](https://mt85-prod.herokuapp.com/Project/logout.php)
    - [ ] \(mm/dd/yyyy of completion) User will be able to see their profile
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/profile.php](https://mt85-prod.herokuapp.com/Project/profile.php)
    - [ ] \(mm/dd/yyyy of completion) User will be able to edit their profile
        - [Milestone 1](https://github.com/MattToegel/IT202/blob/Milestone1/public_html/Project/milestone1.md)
        - [https://mt85-prod.herokuapp.com/Project/profile.php](https://mt85-prod.herokuapp.com/Project/profile.php)
- Milestone 2
  - (duplicate template here for Milestone 1 features)
- Milestone 3
  - (duplicate template here for Milestone 1 features)
- Milestone 4
  - (duplicate template here for Milestone 1 features)
  - 
### Intructions
#### Don't delete this
1. Pick one project type
2. Create a proposal.md file in the root of your project directory of your GitHub repository
3. Copy the contents of the Google Doc into this readme file per the template
4. Convert the list items to markdown checkboxes (apply any other markdown for organizational purposes)
5. Create a new Project Board on GitHub
   - Choose the Automated Kanban Board Template
   - For each major line item (or sub line item if applicable) create a GitHub issue
   - The title should be the line item text
   - The first comment should be the acceptance criteria (i.e., what you need to accomplish for it to be "complete")
   - Leave these in "to do" status until you start working on them
   - Assign each issue to your Project Board (the right-side panel)
   - Assign each issue to yourself (the right-side panel)
6. As you work
  1. As you work on features, create separate branches for the code in the style of Feature-ShortDescription (using the Milestone# branch as the source to branch from and to merge into)
  2. Add, commit, push the related file changes to this branch
  3. Add evidence to the PR (Feat to Milestone) conversation view comments showing the feature being implemented (these will be used to populate the related .md files for each milestone, forgetting to capture this will give you more work later on)
     - Screenshot(s) of the site view (make sure they clearly show the feature)
     - Screenshot of the database data if applicable
     - Describe each screenshot to specify exactly what's being shown
     - A code snippet screenshot or reference via GitHub markdown may be used as an alternative for evidence that can't be captured on the screen
  4. Update the checklist of the proposal.md file for each feature this branch is completing (ideally should be 1 branch/pull request per feature, but some cases may have multiple)
    - Basically add an x to the checkbox markdown along with a date after
      - (i.e.,   - [x] (mm/dd/yy) ....) See Template above
    - Add the pull request link as a new indented line for each line item being completed
    - Attach any related issue items on the right-side panel
  5. Merge the Feature Branch into your Milestone branch (this should close the pull request and the attached issues)
    - Merge the Milestone branch into dev, then dev into prod as needed (be sure it doesn't break anything in prod)
    - Last two steps are mostly for getting it to prod for delivery of the assignment 
  7. If the attached issues don't close wait until the next step
  8. Merge the updated dev branch into your production branch via a pull request
  9. Close any related issues that didn't auto close
    - You can edit the dropdown on the issue or drag/drop it to the proper column on the project board