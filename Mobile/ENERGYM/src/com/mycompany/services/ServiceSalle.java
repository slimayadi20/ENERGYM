/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.services;

import com.codename1.io.CharArrayReader;
import com.codename1.io.ConnectionRequest;
import com.codename1.io.JSONParser;
import com.codename1.io.MultipartRequest;
import com.codename1.io.NetworkEvent;
import com.codename1.io.NetworkManager;
import com.codename1.ui.Dialog;
import com.codename1.ui.events.ActionListener;
import com.mycompany.entities.Reclamation;
import com.mycompany.entities.Salle;
import com.mycompany.entities.SessionManager;
import com.mycompany.utils.Statics;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

/**
 *
 * @author nouri
 */
public class ServiceSalle {

    public ArrayList<Salle> Salle;

    public static ServiceSalle instance = null;
    public boolean resultOK;
    private ConnectionRequest req;

    private ServiceSalle() {
        req = new ConnectionRequest();
    }

    public static ServiceSalle getInstance() {
        if (instance == null) {
            instance = new ServiceSalle();
        }
        return instance;
    }

    public ArrayList<Salle> parseSalle(String jsonText) {
        try {
            Salle = new ArrayList<>();
            JSONParser j = new JSONParser();// Instanciation d'un objet JSONParser permettant le parsing du résultat json

            /*
                On doit convertir notre réponse texte en CharArray à fin de
            permettre au JSONParser de la lire et la manipuler d'ou vient
            l'utilité de new CharArrayReader(json.toCharArray())
           
            La méthode parse json retourne une MAP<String,Object> ou String est
            la clé principale de notre résultat.
            Dans notre cas la clé principale n'est pas définie cela ne veux pas
            dire qu'elle est manquante mais plutôt gardée à la valeur par defaut
            qui est root.
            En fait c'est la clé de l'objet qui englobe la totalité des objets
                    c'est la clé définissant le tableau de tâches.
             */
            Map<String, Object> SalleListJson = j.parseJSON(new CharArrayReader(jsonText.toCharArray()));
            List<Map<String, Object>> list = (List<Map<String, Object>>) SalleListJson.get("root");

            //Parcourir la liste des tâches Json
            for (Map<String, Object> obj : list) {
                //Création des tâches et récupération de leurs données
                Salle t = new Salle();
                float id = Float.parseFloat(obj.get("id").toString());
                t.setId((int) id);
                t.setNom(obj.get("nom").toString());
                t.setAdresse(obj.get("adresse").toString());
                t.setTel(obj.get("tel").toString());
                t.setMail(obj.get("mail").toString());
                t.setDescription(obj.get("description").toString());

                System.out.println("******");
                System.out.println(id);
                System.out.println("******");
                System.out.println("******");
                //Ajouter la tâche extraite de la réponse Json à la liste
                Salle.add(t);
            }

        } catch (IOException ex) {

        }
        return Salle;
    }

    public ArrayList<Salle> getAllSalle() {
        ArrayList<Salle> listReclamation = new ArrayList<>();

        String url = Statics.BASE_URL + "/displaySalleMobile";
        req.setUrl(url);
        req.setPost(false);
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                Salle = parseSalle(new String(req.getResponseData()));
                req.removeResponseListener(this);
            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);
        return Salle;
    }

    public static void addlike() {
        String url = Statics.BASE_URL + "/likeMobile?id=" + 2 + "&salleid=" + 1; //création de l'URL /// add like 

        MultipartRequest req = new MultipartRequest();
        req.setUrl(url);// Insertion de l'URL de notre demande de connexion
        System.out.println(url);
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                req.removeResponseListener(this); //Supprimer cet actionListener
                /* une fois que nous avons terminé de l'utiliser.
                La ConnectionRequest req est unique pour tous les appels de
                n'importe quelle méthode du Service task, donc si on ne supprime
                pas l'ActionListener il sera enregistré et donc éxécuté même si
                la réponse reçue correspond à une autre URL(get par exemple)*/

            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);

    }

    public boolean addSalle(Salle t) {
        String url = Statics.BASE_URL + "/ajoutSalleMobile?nom=" + t.getNom() + "&adresse=" + t.getAdresse() + "&tel=" + t.getTel() + "&mail=" + t.getMail() + "&description=" + t.getDescription() ; //création de l'URL
        req.setUrl(url);// Insertion de l'URL de notre demande de connexion
        System.out.println(url);
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                resultOK = req.getResponseCode() == 200; //Code HTTP 200 OK
                req.removeResponseListener(this); //Supprimer cet actionListener
                /* une fois que nous avons terminé de l'utiliser.
                La ConnectionRequest req est unique pour tous les appels de 
                n'importe quelle méthode du Service task, donc si on ne supprime
                pas l'ActionListener il sera enregistré et donc éxécuté même si 
                la réponse reçue correspond à une autre URL(get par exemple)*/

            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);
        return resultOK;
    }

    public void deletSalle(float id) {

        Dialog d = new Dialog();
        if (d.show("Delete salle", "Do you really want to remove this salle", "Yes", "No")) {

            req.setUrl(Statics.BASE_URL + "/SupprimerSalleMobile?id="+id);
            //System.out.println(Statics.BASE_URL+"/deleteReclamationMobile?id="+id);
            NetworkManager.getInstance().addToQueueAndWait(req);

            d.dispose();
        }
    }

    public boolean updateSalle(Salle t) {
        String url = Statics.BASE_URL + "/ModifierSalleMobile?id=" + t.getId() + "&nom=" + t.getNom() + "&adresse=" + t.getAdresse() + "&tel=" + t.getTel() + "&mail=" + t.getMail() + "&description=" + t.getDescription() ; //création de l'URL
        System.out.println(url);
        System.out.println(t.getId());
        req.setUrl(url);// Insertion de l'URL de notre demande de connexion
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                resultOK = req.getResponseCode() == 200; //Code HTTP 200 OK
                req.removeResponseListener(this); //Supprimer cet actionListener
            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);
        return resultOK;
    }
}
