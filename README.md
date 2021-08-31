# Heroku Setup

- 08/30/2021 removed .htaccess and updated Procfile to use public_html as docroot
- Profile tells Heroku how to deploy
- Composer.json mentions what libraries will be used 
- public_html contains all public facing content
- partials will be templates/partial pages that will NOT be accessed directly (still can reference via code)
- lib will be custom functions/libraries/etc that will NOT be accessed directly (still can be referenced via code)
- All work will be subfolders inside public_html (for the most part), lib will contain reusable functionality, partials will contain reusable templates, nothing else should change.
