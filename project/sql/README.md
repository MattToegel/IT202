# SQL folder
- In this directory, we'll keep any structure change SQL files.
- We'll be using a migration tool, which will also be in this directory, that'll find the necessary files and attempt to execute them to restore/migrate them.
- We'll want to prefix the filenames with something that'll put them in a sortable order so everything runs in the expected order.
    - I recommend starting with 000_ in the file names (if we don't left pad zeros the sorting messes up after 9_ )
    - Any time you need to execute new files just trigger init_db.php