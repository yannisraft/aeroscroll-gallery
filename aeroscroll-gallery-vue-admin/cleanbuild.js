const fs = require('fs');

console.log(">> Searching app.css file...");


function delay(time) {
    return new Promise(resolve => setTimeout(resolve, time));
  }

async function ProcessCssFile() {
    try {
        
        var appcsspath = "../admin/dist/css/app.css"; 

        if (fs.existsSync(appcsspath)) {    
          console.log(">> Found app.css file...");
          const data = await fs.readFileSync(appcsspath, 'utf8');
      
          console.log(">> Processing app.css file...");
          //console.log(data);
          await delay(500);
      
          var result = data.replace(/body{min/g, '#wp-vue-app-admin body{min');
          result = result.replace(/h1{font/g, '#wp-vue-app-admin h1{font');
          result = result.replace(/h2{font/g, '#wp-vue-app-admin h2{font');
          result = result.replace(/h3{font/g, '#wp-vue-app-admin h3{font');
          result = result.replace(/h4{font/g, '#wp-vue-app-admin h4{font');
          result = result.replace(/h5{font/g, '#wp-vue-app-admin h5{font');
          result = result.replace(/h6{font/g, '#wp-vue-app-admin h6{font');
          result = result.replace(/p{margin/g, '#wp-vue-app-admin p{margin');
      
          console.log(">> Writing to app.css file...");
          await fs.writeFileSync(appcsspath, result,'utf8');
          await delay(500);

          /* console.log(">> Copying files to admin folder...");

          // Copy app.css
          fs.copyFileSync(appcsspath, "../admin/css/app.css");

          // Copy app.js
          fs.copyFileSync("./dist/js/app.js", "../admin/js/app.js");

          // Copy chunk-vendors.js
          fs.copyFileSync("./dist/js/chunk-vendors.js", "../admin/js/chunk-vendors.js"); */
      
          console.log(">> Finished...");
        }
      } catch(err) {
        console.error(">> Error occured: ",err);
      }
} 

ProcessCssFile();

