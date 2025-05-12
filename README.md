### Resources Needed to run our DigitizedLibraryManagementSystem_Demo
Docker - https://www.docker.com/products/docker-desktop/
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
In order to run our project locally you first need
1. Turn on SQL DAtabase on Render.com
2. Create postgree SQL Database on Azure Data Studio
3. Get ENV variables from SQL server on render.com
4. Input ENV variables from rencer.com into postgree SQL Database on Azure
Data Studio
5. Install Git in order to use git bash in visual studio.
6. Load DigitizedLibraryManagementSystem_Demo Github files locally on the
XAMPP/htdocs/ folder. The XAMPP Folder is usually in the local disk.
7. ADD Render.com ENV variables as a FILE to
XAMPP/htdocs/DigitizedLibraryManagementSystem_Demo Files.
8. Turn on XAMPP apache server.
9. use localhost/DigitizedLibraryManagementSystem_Demo
Our group was able to create a SQL database with inputs using (Azure Data
Studio) which was connected to a SQL database server on Render.com. The
SQL render server auto created some ENV variables which are needed to
connect Azure Data Studio to render.com
After connecting the Database and the server. Then it is time to configure
the ENV variables within our local files so that our API files can access
the online database.Although our entire project technically runs locally,
we are actually accessing a online database when we run our local project.
The server that we used for the demo might still be running. Although it
is unlikely because we used a free trial to create a demo.
If its still active, then the ENV variables we used will be down below.
### ENV (Environment) variables From Render.com:
### Use visual studio - Bash on terminal to clone the Github Repository
using this command:
git clone
https://github.com/MatthewH2019/DigitizedLibraryManagementSystem_Demo.git
