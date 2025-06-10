const FtpDeploy = require("ftp-deploy");
const inquirer = require("inquirer");
const ftpDeploy = new FtpDeploy();
require("dotenv").config({ path: "./.env.local" });

inquirer
  .prompt([
    {
      type: "list",
      name: "env",
      message: "Choice upload env",
      choices: ["staging", "production"],
    },
  ])
  .then((answers) => {
    const env = answers.env;
    const config = process.env;

    let host, user, pass, remote;
    let cancelable = false;
    let local = config["DEPLOY_LOCAL_ROOT"];
    if (!local) {
      console.error("Err. Local dir config undefined");
      cancelable = true;
    }
    local = __dirname + local;

    if (env === "staging") {
      host = config["DEPLOY_STG_FTP_HOST"];
      if (!host) host = config["DEPLOY_PROD_FTP_HOST"];
      user = config["DEPLOY_STG_FTP_USER"];
      if (!user) user = config["DEPLOY_PROD_FTP_USER"];
      pass = config["DEPLOY_STG_FTP_PASS"];
      if (!pass) pass = config["DEPLOY_PROD_FTP_PASS"];
      remote = config["DEPLOY_STG_ROOT"];
      if (!remote) remote = config["DEPLOY_PROD_ROOT"];
    } else if (env === "production") {
      host = config["DEPLOY_PROD_FTP_HOST"];
      user = config["DEPLOY_PROD_FTP_USER"];
      pass = config["DEPLOY_PROD_FTP_PASS"];
      remote = config["DEPLOY_PROD_ROOT"];
    }
    if (!host) {
      console.error("Err. Host config undefined");
      cancelable = true;
    }
    if (!user) {
      console.error("Err. User config undefined");
      cancelable = true;
    }
    if (!pass) {
      console.error("Err. Password config undefined");
      cancelable = true;
    }
    if (!remote) {
      console.error("Err. Remote dir config undefined");
      cancelable = true;
    }
    if (cancelable === true) {
      console.error("-- error. Deploy Cancel. --");
      return 0;
    }
    console.log("Deploy to " + env);
    console.log("upload files from: " + local);
    console.log("upload files to: " + host + " " + remote);

    inquirer
      .prompt([
        {
          type: "confirm",
          name: "confirm",
          message: "Are you sure you want to upload this?",
          default: true,
        },
      ])
      .then((answers) => {
        if (answers.confirm !== true) {
          console.log("Deploy canceled.");
          return 0;
        }
        console.log("Deploy start!!");
        ftpDeploy
          .deploy({
            user: user,
            password: pass,
            host: host,
            localRoot: local,
            remoteRoot: remote,
            include: ["*", "**/*"],
            deleteRemote: false,
            forcePasv: true,
          })
          .then((res) => {
            console.log("Deploy Finished!!", res);
          })
          .catch((err) => console.log(err));

        ftpDeploy.on("uploading", (data) => {
          console.log(
            "Upload file count: ",
            `${data.transferredFileCount} / ${data.totalFilesCount}`
          );
          console.log("Uploading: ", data.filename);
        });
      });
  });
