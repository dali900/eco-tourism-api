## 2. Git setup

create git bare
```sh
cd /var/www/
mkdir repo-project-api.git
cd repo-project-api.git 
git init --bare
touch hooks/post-receive
chmod +x hooks/post-receive
```

api
```sh
#!/bin/bash
# Location of our bare repository.
GIT_DIR="/var/www/repo-amp-api.git"

# Where we want to copy our code.
TARGET="/var/www/amp-api"

while read oldrev newrev ref
do
   # We gonna do stuff.
   # Neat trick to get the branch name of the reference just pushed:
   BRANCH=$(git rev-parse --symbolic --abbrev-ref $ref)

   # Send a nice message to the machine pushing to this remote repository.
   echo "Push received! Deploying branch: ${BRANCH}..."

   # "Deploy" the branch we just pushed to a specific directory.
   git --work-tree=$TARGET --git-dir=$GIT_DIR checkout -f $BRANCH
done

cd $TARGET
echo "Run migrations"
php artisan migrate --env=local
echo "Artisan optimize. Caching..."
php artisan optimize
echo "Done!"
```
app

```sh
#!/bin/bash
# Location of our bare repository.
GIT_DIR="/var/www/repo-amp-app.git"

# Where we want to copy our code.
TARGET="/var/www/amp-app"
RELEASE_FOLDER="/var/www/actamedia.net"
BUILD="build"
while read oldrev newrev ref
do
   # We gonna do stuff.
   # Neat trick to get the branch name of the reference just pushed:
   BRANCH=$(git rev-parse --symbolic --abbrev-ref $ref)

   # Send a nice message to the machine pushing to this remote repository.
   echo "Push received! Deploying branch: ${BRANCH}..."

   # "Deploy" the branch we just pushed to a specific directory.
   git --work-tree=$TARGET --git-dir=$GIT_DIR checkout -f $BRANCH
done

#Load NVM (for error command npm not found)
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" 

cd $TARGET
echo "Delete dist folder"
rm -rf $TARGET/dist
echo "Install dependencies"
nvm use 18
npm install --legacy-peer-deps
echo "env build: $BUILD"
echo "Building app..."
npm run $BUILD
cd $RELEASE_FOLDER/versions
old=$(ls -r | head -1)
new=0
let "new=old+1"
mkdir $RELEASE_FOLDER/versions/$new
echo "Switching to folder build: $new"
cp -R $TARGET/* $RELEASE_FOLDER/versions/$new

#create link to dist folder from build folder(TARGET) for pdf previews folders
rm -rf $RELEASE_FOLDER/versions/$new/dist
ln -s $TARGET/dist $RELEASE_FOLDER/versions/$new/

#switch to new build version
rm $RELEASE_FOLDER/current/dist
ln -s $RELEASE_FOLDER/versions/$new/dist $RELEASE_FOLDER/current/

#delete old build
rm -rf $RELEASE_FOLDER/versions/$old
echo "Done!"

```

Before pushing code to live prepare working directory
```sh
cd /var/www/
mkdir selonatriklika.rs
mkdir selonatriklika.rs/current
mkdir selonatriklika.rs/versions
mkdir selonatriklika.rs/versions/1
sudo chown eco:www-data -R selonatriklika.rs/
```

## Add Git remote
local git
```sh
git remote -v
#api
git remote add live ssh://eco@185.119.90.254/var/www/repo-eco-tourism-api.git
#app
git remote add live ssh://eco@185.119.90.254/var/www/repo-eco-tourism-app.git
#izmena remote-a
git remote set-url live ssh://amp@185.119.90.39/var/www/repo-eco-tourism-app.git

```
