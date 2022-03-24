/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.entities;

/**
 *
 * @author MSI
 */
public class Participants {
    float idevent; 
float id ; 
float iduser ; 

    public Participants(float idevent, float id, float iduser) {
        this.idevent = idevent;
        this.id = id;
        this.iduser = iduser;
    }

    public Participants() {
    }

    public float getIdevent() {
        return idevent;
    }

    public void setIdevent(float idevent) {
        this.idevent = idevent;
    }

    public float getId() {
        return id;
    }

    public void setId(float id) {
        this.id = id;
    }

    public float getIduser() {
        return iduser;
    }

    public void setIduser(float iduser) {
        this.iduser = iduser;
    }

}
