# IT 202 Project Proposal

## Project Name: Interactive Story
### Project Summary: Users will be able to participate in numerous interactive stories where they get to pick from a set of choices to see how their journey progresses. Users will also have the ability to create their own stories for others to read.

# Features:
- [x] User will be able to register a new account
- [x] User will be able to login to their account provided they enter the correct credentials
- [x] User will be able to logout (session will be destroyed)
- [x] User will be able to see their profile
- [ ] User will be able to reset their password
- [x] Passwords will not be stored/handled in plaintext
- [x] User will not be able to access restricted pages
- [x] Anonymous users will not be able to access private pages
##### [Start listing your project specific features here, follow the format above and keep each line concise. Think of anything that can be done in your app and write it here, this will be used as a checklist for development and for grading. I’ll review the list with you and narrow or broaden scope as necessary.]
- [x] Users with the viewer role (Edit: no special roles, non-admin users can do the same stuff)
    - [x] Will be able to search for stories by title and/or author
    - [x] Will be able to load a story (to begin reading)
    - [x] Will be able to pick a choice to progress to the next outcome
    - [x] Will be able to reset their progress for a story back to the beginning
        - Currently only when they reach an end do they get the decision, but the url can be navigated to manually to reset if known.
    - [x] Will be able to bookmark their progress so they can come back to where they left off
        - This is done automatically to prevent going back and trying other paths.
                                      - [x] Will be able to see a list of stories they have progress in
                                      - [ ] Will be able to favorite/mark for later stories
                                      - [ ] Will be able to see a list of marked stories
                                      - [ ] Will be able to remove stories from either list
                                  - [x] Users with the writer role (Edit: see viewer role note)
                                      - [x] Will be able to create a new story under their ownership
                                          - [x] First page requires title, description
                                          - See section about what attributes a story will have (down below)
                                          - [x] Will be able to add choices to each story arc
                                          - [x] End paths can be set to either go back to the previous stage or strictly force the reader to start over (if they choose)
                                          - [x] Will be able to reassign which choice targets which next arc
                                          - [x] Will be able to edit/revise previously posted arcs
                                          - [x] Will be able to edit/revise previously set choices
                                          - [x] Will be able to mark a story as public, private, draft
                                          - [x] Will be able to delete their stories
        - [x] Will be able to delete their choice (deleted via Deleting Arcs)
        - [x] Stories will have visibility options (this is same as public/private/draft)
            - [x] Public will show in searches
            - [x] Private will not show in searches but direct links will have access
            - [x] Draft will not be viewable to anyone other than the original author
        - [ ] Stories will have a number of pages that will be linked to via the given choices
        - [x] Pages(Arcs) will have a title, story text, and choices
        - [ ] Stories will show how many people favorited it
        - [ ] Stories will show number of people interacting with it
            - Where a user made at least 1 choice
        - [ ] Stories will show title, summary, author, created, updated timestamp, genre
        - [x] Readers will not be able to go back once a choice is made
            -Exception is if they reach an end, then based on the writer they can either link back to the previous stage or start over
        - [ ] Main page will show stories in various categories
            - [ ] Top 10 Stories
            - [ ] 10 Newest Stories
            - [ ] 10 Most recently updated
        - [x] Anonymous users can participate but their history won’t be tracked and they’ll lose all progress once their session ends

Requirements:
Your application needs to handle a number of users
Each user has a profile
You application should have some extra data related to your user based on what you’re implementation goal is, for example: wishlist/shopping cart for E-Commerce site, player stats for a game, wins/losses for a game, posts for a blog/chat, etc
This data should follow CRUD (Create, Read, Update, Delete)
The site administrator(s) could potentially be the only ones with certain permissions such as delete, but your app should still support this functionality)
You’ll be using MySQL backend (provided by NJIT AFS)
Your SQL scripts should be stored on github
If using the init_db sample keep your structural changes under a folder called “sql” and keep any data changes or queries under “queries”
Your project will be hosted on your NJIT AFS site
