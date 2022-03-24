/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.services;

import com.codename1.io.CharArrayReader;
import com.codename1.io.ConnectionRequest;
import com.codename1.io.JSONParser;
import com.codename1.io.MultipartRequest;
import com.codename1.io.NetworkManager;
import com.codename1.ui.Dialog;
import com.codename1.ui.TextField;
import com.codename1.ui.util.Resources;
import com.mycompany.entities.SessionManager;
import com.mycompany.entities.User;
import com.mycompany.gui.NewsfeedForm;
import com.mycompany.gui.back.NewsfeedFormBack;
import com.mycompany.utils.Statics;
import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.Map;

/**
 *
 * @author MSI
 */
public class ServiceUser {

    public ArrayList<User> User;

    public static ServiceUser instance = null;
    public boolean resultOK;
    private ConnectionRequest req;
    String json;

    private ServiceUser() {
        req = new ConnectionRequest();
    }

    public static ServiceUser getInstance() {
        if (instance == null) {
            instance = new ServiceUser();
        }
        return instance;
    }

    public void signup(TextField nom, TextField prenom, TextField email, TextField password, TextField confirmPassword, TextField Phone, Resources res) {
        String url = Statics.BASE_URL + "/signupMobile?email=" + email.getText().toString() + "&nom=" + nom.getText().toString() + "&prenom=" + prenom.getText().toString() + "&password=" + password.getText().toString() + "&phoneNumber=" + Phone.getText().toString();
        req.setUrl(url);
        System.out.println(url);
        if (email.getText().equals(" ") && password.getText().equals(" ")) {
            Dialog.show("erreur", "veuillez remplir les champs", "ok", null);
        }
        req.addResponseListener((e) -> {
            byte[] data = (byte[]) e.getMetaData();
            String responseData = new String(data);
            System.out.println("data===>" + responseData);
        });
        NetworkManager.getInstance().addToQueueAndWait(req);

    }

    public void signin(TextField email, TextField password, Resources res) {
        String url = Statics.BASE_URL + "/signinMobile?email=" + email.getText().toString() + "&password=" + password.getText().toString();
        req.setUrl(url);
        System.out.println(url);

        req.addResponseListener((e) -> {
            JSONParser j = new JSONParser();
            String json = new String(req.getResponseData()) + "";
            try {
                if (json.equals("failed")) {
                    Dialog.show("echec d'authentification", "username or password incorrect", "OK", null);

                } else {
                    System.out.println("data ==" + json);
                    Map<String, Object> user = j.parseJSON(new CharArrayReader(json.toCharArray()));
                    java.util.List<String> role = (java.util.List<String>) user.get("roles");

                    //  Map<String, Object> role = j.parseJSON((Reader) user.get("roles"));
//String role=(user.get("roles").getJsonObject(0)) ;
                    Float status = Float.parseFloat(user.get("status").toString());
                    System.out.println(role.get(0));
                    if (status == 0) {
                        Dialog.show("error", "you are banned", "ok", null);
                    } else if (status == 2) {
                        Dialog.show("error", "wait until we approve your subscription by then you are simple user ", "ok", null);
                        new NewsfeedForm(res).show();
                    }
                    if (!user.isEmpty() && status == 1 && "ROLE_ADMIN".equals(role.get(0))) {
                        new NewsfeedFormBack(res).show();// yemchi lel home yelzem nrigelha
                        SessionManager.setEmail(user.get("email").toString());
                        float id = Float.parseFloat(user.get("id").toString());
                        SessionManager.setId((int) id);
                        //      SessionManager.setId();// hedhi mochkla
                        SessionManager.setNom(user.get("nom").toString());
                        SessionManager.setPrenom(user.get("prenom").toString());
                        SessionManager.setPassword(user.get("password").toString());
                    }
                    if (!user.isEmpty() && status == 1 && "ROLE_USER".equals(role.get(0))) {
                        new NewsfeedForm(res).show();// yemchi lel home yelzem nrigelha
                        SessionManager.setEmail(user.get("email").toString());
                        float id = Float.parseFloat(user.get("id").toString());
                        SessionManager.setId((int) id);
                        //      SessionManager.setId();// hedhi mochkla
                        SessionManager.setNom(user.get("nom").toString());
                        SessionManager.setPrenom(user.get("prenom").toString());
                        SessionManager.setPassword(user.get("password").toString());
                    }
                }
            } catch (Exception ex) {
                ex.printStackTrace();
            }
        });

        NetworkManager.getInstance().addToQueueAndWait(req);

    }

    public String getPasswordbyPhone(String phoneNumber, Resources res) {
        String url = Statics.BASE_URL + "/passwordMobile?phoneNumber=" + phoneNumber;
        req.setUrl(url);
        System.out.println(url);

        req.addResponseListener((e) -> {
            JSONParser j = new JSONParser();
            json = new String(req.getResponseData()) + "";
            try {

                System.out.println("data ==" + json);
                Map<String, Object> password = j.parseJSON(new CharArrayReader(json.toCharArray()));

            } catch (Exception ex) {
                ex.printStackTrace();
            }
        });

        NetworkManager.getInstance().addToQueueAndWait(req);
        return json;
    }

    public static void EditUser(String nom, String prenom, String email, String password, String imageFile) {
        String url = Statics.BASE_URL + "/editUserMobile?&email=" + email + "&password=" + password + "&nom=" + nom + "&prenom=" + prenom + "&imageFile=" + imageFile;
        MultipartRequest req = new MultipartRequest();
        req.setUrl(url);
        req.setPost(true);
        req.addArgument("nom", nom);
        req.addArgument("prenom", prenom);
        req.addArgument("password", password);
        req.addArgument("email", email);
        req.addResponseListener((response) -> {
            byte[] data = (byte[]) response.getMetaData();
            String a = new String(data);
            System.out.println(a);
            if (a.equals("success")) {
            } else {
                // Dialog.show("erreur", "echec de modification", "OK", null);
            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);

    }

}
