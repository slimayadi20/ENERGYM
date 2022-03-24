/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.mycompany.services;

import com.codename1.io.CharArrayReader;
import com.codename1.io.ConnectionRequest;
import com.codename1.io.JSONParser;
import com.codename1.io.NetworkEvent;
import com.codename1.io.NetworkManager;
import com.codename1.ui.Dialog;
import com.codename1.ui.events.ActionListener;
import com.mycompany.entities.Cours;
import com.mycompany.entities.Reclamation;
import com.mycompany.utils.Statics;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

/**
 *
 * @author nouri
 */
public class ServiceCours {
  public ArrayList<Cours> Cours;
   
    public static ServiceCours instance=null;
    public boolean resultOK;
    private ConnectionRequest req;

    private ServiceCours() {
         req = new ConnectionRequest();
    }
        public static ServiceCours getInstance() {
        if (instance == null) {
            instance = new ServiceCours();
        }
        return instance;
    }
  public ArrayList<Cours> parseCours(String jsonText){
        try {
            Cours=new ArrayList<>();
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
            Map<String,Object> CoursListJson = j.parseJSON(new CharArrayReader(jsonText.toCharArray()));
             List<Map<String,Object>> list = (List<Map<String,Object>>)CoursListJson.get("root");
         
   //Parcourir la liste des tâches Json
            for(Map<String,Object> obj : list){
                //Création des tâches et récupération de leurs données
                Cours t = new Cours();
                float id = Float.parseFloat(obj.get("id").toString());
                t.setId((int)id);
                t.setNom(obj.get("nom").toString());
                t.setDescription(obj.get("description").toString());
                t.setNombre(obj.get("nombre").toString());
                t.setNomCoach(obj.get("nomCoach").toString());
             
                t.setJour(obj.get("jour").toString());
               
                        System.out.println("******");
                        System.out.println(id);
                        System.out.println("******");
                        System.out.println("******");
                //Ajouter la tâche extraite de la réponse Json à la liste
                Cours.add(t);
            }
           
           
        } catch (IOException ex) {
           
        }
            return Cours;
    }
    public ArrayList<Cours> getAllCours(float id){
        ArrayList<Cours> listReclamation = new ArrayList<>();

        String url = Statics.BASE_URL+"/SallecoursMobile?id="+id;
        req.setUrl(url);
        req.setPost(false);
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                Cours = parseCours(new String(req.getResponseData()));
                req.removeResponseListener(this);
            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);
        return Cours;
}
    public ArrayList<Cours> getAllCoursBack(){
        ArrayList<Cours> listReclamation = new ArrayList<>();

        String url = Statics.BASE_URL+"/SallecoursMobileBack";
        req.setUrl(url);
        req.setPost(false);
        req.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                Cours = parseCours(new String(req.getResponseData()));
                req.removeResponseListener(this);
            }
        });
        NetworkManager.getInstance().addToQueueAndWait(req);
        return Cours;
}
    public boolean addCours(Cours t) {
        String url = Statics.BASE_URL+"/ajoutCoursMobile?nom=" + t.getNom()+ "&nomCoach="+t.getNomCoach()+"&description="+t.getDescription()+"&nombre="+t.getNombre()+"&salleassocie="+t.getSalleassocie()+"&jour="+t.getJour(); //création de l'URL
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
       public boolean updateCours(Cours t) {
 String url = Statics.BASE_URL + "/ModifierCoursMobile?id="+t.getId() +"&nom="+ t.getNom()+ "&nomCoach=" + t.getNomCoach()+ "&description=" + t.getDescription()+ "&nombre=" + t.getNombre()+ "&jour=" + t.getJour();
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
        return resultOK;    }
            public void deletCours(float id){  
       
        Dialog d = new Dialog();
            if(d.show("Delete cours","Do you really want to remove this reclamation","Yes","No"))
            {            
               
                req.setUrl(Statics.BASE_URL+"/SupprimerCoursMobile?id="+id);
                //System.out.println(Statics.BASE_URL+"/deleteReclamationMobile?id="+id);
                NetworkManager.getInstance().addToQueueAndWait(req);
               
                d.dispose();
            }
    }
 

}
