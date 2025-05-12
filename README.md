### Resources Needed to run DigitizedLibraryManagementSystem_Demo
XAMPP - https://www.apachefriends.org/download.html
Azure Data Studio -
https://azure.microsoft.com/en-us/products/cosmos-db/?ef_id=_k_Cj0KCQjw8vv
ABhCcARIsAOCfwwpx0JVek7L58e5dd_GmKJJhvKJC6Ell9ZzKFCvcn27eTfC34ktOJ9QaAhBJE
ALw_wcB_k_&OCID=AIDcmm5edswduu_SEM__k_Cj0KCQjw8vvABhCcARIsAOCfwwpx0JVek7L5
8e5dd_GmKJJhvKJC6Ell9ZzKFCvcn27eTfC34ktOJ9QaAhBJEALw_wcB_k_&gad_source=1&g
ad_campaignid=21496728177&gbraid=0AAAAADcJh_vVuRgbO3VD34Fnoh6QANHXe&gclid=
Cj0KCQjw8vvABhCcARIsAOCfwwpx0JVek7L58e5dd_GmKJJhvKJC6Ell9ZzKFCvcn27eTfC34k
tOJ9QaAhBJEALw_wcB
Render.com - https://render.com/
git - https://git-scm.com/downloads/win
Visual Studio - https://code.visualstudio.com/download

### Set up for local hosting:
1. Activate free PostgresSQL online database hosted on [Render](render.com) or connect to existing database with provided ENV variables.[Render Documention:](https://render.com/docs/postgresql-creating-connecting)
2. For database management, create a connection to Render database server through Azure Data Studio.
![connect](https://github.com/user-attachments/assets/f1830c1b-9cf0-44e5-bb9a-42a92cf5d66f)
3. Turn on and connect to local XAMPP APACHE server.
4. Install Git in order to use git bash in visual studio.
5. Initialize the DigitizedLibraryManagementSystem_Demo repository files within the
C:/XAMPP/htdocs/ folder.
6. Create and configure htaccess file like so:
![htaccess_configuration](https://github.com/user-attachments/assets/24e3e8d0-ee02-43e8-944f-e2121104ef7e)
7. Connect through browser: 

Our group was able to create a SQL database with inputs using (Azure Data
Studio) which was connected to a SQL database server on Render.com. The
SQL render server auto created some ENV variables which are needed to
connect Azure Data Studio to render.com
After connecting the Database and the server. Then it is time to configure
the ENV variables within our local files so that our API files can access
the online database. Although our entire project technically runs locally,
we are actually accessing a online database when we run our local project.
The server that we used for the demo might still be running. Although it
is unlikely because we used a free trial to create a demo.
If its still active, then the ENV variables we used will be down below.
### ENV (Environment) variables From Render.com:

### Use visual studio - Bash on terminal to clone the Github Repository
using this command:
git clone
https://github.com/MatthewH2019/DigitizedLibraryManagementSystem_Demo.git
